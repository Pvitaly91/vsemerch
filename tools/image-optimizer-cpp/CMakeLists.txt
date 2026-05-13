cmake_minimum_required(VERSION 3.21)

project(vsemerch_image_optimizer LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 17)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_CXX_EXTENSIONS OFF)

add_executable(vsemerch-image-optimizer
    src/main.cpp
    src/CliOptions.cpp
    src/FileScanner.cpp
    src/ImageOptimizer.cpp
    src/SafeReplace.cpp
)

target_include_directories(vsemerch-image-optimizer PRIVATE src)

if(MSVC)
    target_compile_options(vsemerch-image-optimizer PRIVATE /W4 /permissive- /EHsc /utf-8)
else()
    target_compile_options(vsemerch-image-optimizer PRIVATE -Wall -Wextra -Wpedantic)
endif()

find_package(unofficial-vips CONFIG QUIET)

set(VIPS_ROOT "" CACHE PATH "Optional libvips development root, for example a vips-dev-* directory")

if(TARGET unofficial::vips::vips)
    target_link_libraries(vsemerch-image-optimizer PRIVATE unofficial::vips::vips)
elseif(VIPS_ROOT)
    target_include_directories(vsemerch-image-optimizer PRIVATE
        "${VIPS_ROOT}/include"
        "${VIPS_ROOT}/include/glib-2.0"
        "${VIPS_ROOT}/lib/glib-2.0/include"
    )

    find_library(VIPS_LIBRARY NAMES vips libvips PATHS "${VIPS_ROOT}/lib" NO_DEFAULT_PATH REQUIRED)
    find_library(GLIB_LIBRARY NAMES glib-2.0 libglib-2.0 PATHS "${VIPS_ROOT}/lib" NO_DEFAULT_PATH REQUIRED)
    find_library(GOBJECT_LIBRARY NAMES gobject-2.0 libgobject-2.0 PATHS "${VIPS_ROOT}/lib" NO_DEFAULT_PATH REQUIRED)
    find_library(GIO_LIBRARY NAMES gio-2.0 libgio-2.0 PATHS "${VIPS_ROOT}/lib" NO_DEFAULT_PATH REQUIRED)

    target_link_libraries(vsemerch-image-optimizer PRIVATE
        "${VIPS_LIBRARY}"
        "${GOBJECT_LIBRARY}"
        "${GIO_LIBRARY}"
        "${GLIB_LIBRARY}"
    )

    if(WIN32)
        file(GLOB VIPS_ROOT_DLLS "${VIPS_ROOT}/bin/*.dll")
        add_custom_command(TARGET vsemerch-image-optimizer POST_BUILD
            COMMAND ${CMAKE_COMMAND} -E copy_if_different ${VIPS_ROOT_DLLS} $<TARGET_FILE_DIR:vsemerch-image-optimizer>
            COMMAND_EXPAND_LISTS
        )
    endif()
else()
    find_package(PkgConfig REQUIRED)
    pkg_check_modules(VIPS REQUIRED IMPORTED_TARGET vips)
    target_link_libraries(vsemerch-image-optimizer PRIVATE PkgConfig::VIPS)
endif()
