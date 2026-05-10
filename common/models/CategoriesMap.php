<?


namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class CategoriesMap extends ActiveRecord
{
    public static function tableName()
    {
        return 'categories_map';
    }

    public function rules()
    {
        return [
            [['partner_slug', 'site_slug', 'partner', 'created_at', 'type'], 'required'],
            [['partner_slug', 'site_slug', 'partner', 'type'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }

    // Optional: Add any other custom methods or behaviors for the model here.
}
