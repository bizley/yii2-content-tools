<?php

namespace app\example;

use Yii;
use yii\web\Controller;

/**
 * This controller handles five actions:
 * - 3 generic ones to manipulate images
 * - 2 specific ones for displaying content and saving it.
 */
class ExampleController extends Controller
{
    /**
     * Here is the setting for image handling ContentTools actions.
     * Simple versions of these are provided by yii2-content-tools so let's just use them.
     * Remember to configure widget to know where to find them.
     * @return array
     */
    public function actions()
    {
        return [
            'image-upload' => \bizley\contenttools\actions\UploadAction::class,
            'image-insert' => \bizley\contenttools\actions\InsertAction::class,
            'image-rotate' => \bizley\contenttools\actions\RotateAction::class,
        ];
    }

    /**
     * This action handles displaying the content.
     * Here we fetch content stored for URL '/example/show' (or null if it's not saved yet) and pass it to the view.
     * @return string
     */
    public function actionShow()
    {
        $model = Content::findOne(['page' => '/example/show']);

        return $this->render('show', ['model' => $model]);
    }

    /**
     * This action handles saving the content.
     * Actual data is saved in database and represented by Content model.
     * @return string
     */
    public function actionSave()
    {
        if (Yii::$app->request->isPost) {
            // By default widget sends page key indicating the URL where the data was changed.
            $page = Yii::$app->request->post('page');

            // In case data has been previously saved let's find it for an update
            $model = Content::findOne(['page' => $page]);

            // In case it's not found because this is first time of saving it let's take new object instance
            if ($model === null) {
                $model = new Content();
            }

            // By default yii2-content-tools saves each editable region in 'contentToolsX' POST array key
            // where X is the next number starting from 0. In this example we've got only one region.
            $model->setAttributes([
                'data' => Yii::$app->request->post('contentTools0'),
                'page' => $page,
            ]);

            if (!$model->save()) {
                return $this->asJson(['errors' => $model->errors]);
            }

            return $this->asJson(true);
        }

        return $this->asJson(['errors' => ['No data sent in POST.']]);
    }
}

use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

/**
 * This model represents data stored in DB.
 * In example let's use very simple table with only two columns.
 *
 * @property string $page Primary key of the table, identifies page URL
 * @property string $data Stored content
 */
class Content extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * In this example validation rules are simple:
     * - data and page are required,
     * - data is filtered using HtmlPurifier to prevent malicious HTML being saved,
     * - data must be a string with minimum 1 character length after purification,
     * - page must one of the allowed URLs (in this example only one).
     * @return array
     */
    public function rules()
    {
        return [
            [['page', 'data'], 'required'],
            ['data', 'filter', 'filter' => static function ($value) {
                return HtmlPurifier::process($value);
            }],
            ['data', 'string', 'min' => 1],
            ['page', 'in', 'range' => ['/example/show']],
        ];
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * 'show' view file below
 * ---------------------------------------------------------------------------------------------------------------------
 */

use bizley\contenttools\ContentTools;

/* @var $model Content */
?>

<div id="show-example">
    <?php ContentTools::begin([
        'imagesEngine' => [
            'upload' => '/example/image-upload',
            'rotate' => '/example/image-rotate',
            'insert' => '/example/image-insert',
        ],
        'saveEngine' => [
            'save' => '/example/save',
        ]
    ]); ?>
        <?php if ($model): ?>
            <?= $model->data ?>
        <?php else: ?>
            <h2>This is heading example</h2>
            <p>Here is the default text that can be changed using yii2-content-tools.</p>
        <?php endif; ?>
    <?php ContentTools::end(); ?>
</div>
