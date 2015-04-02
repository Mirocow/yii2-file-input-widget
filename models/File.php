<?php

    namespace mirocow\fileinput\models;

    use Yii;
    use \yii\db\ActiveRecord;

    /**
     * This is the model class for table "tbl_file".
     *
     * @property integer $id
     * @property integer $user_id
     * @property string $name
     * @property string $original_name
     * @property string $path
     * @property string $create_time
     * @property string $entity_type
     * @property integer $entity_id
     * @property string $mime_type
     * @property string $ext
     * @property integer $order
     * @property string $title
     *
     * @property User $user
     */
    class File extends \yii\db\ActiveRecord
    {
        public $fileTypes = '*.*';

        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tbl_file';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
              [['user_id', 'create_time', 'entity_id', 'entity_type', 'mime_type'], 'required'],
              [['user_id', 'entity_id', 'order'], 'integer'],
              [['create_time', 'update_time', 'order'], 'safe'],
              [['name', 'original_name', 'entity_type', 'path', 'title'], 'string', 'max' => 255],
              [['mime_type'], 'string', 'max' => 20],
              [['data'], 'string']
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
              'id' => 'ID',
              'user_id' => 'User ID',
              'name' => 'File name',
              'original_name' => 'Original file name',
              'path' => 'Path',
              'create_time' => 'Created datetime',
              'update_time' => 'Updated datatime',
              'entity_type' => 'Entity type',
              'entity_id' => 'Entity ID',
              'mime_type' => 'Mime тип',
              'order' => 'Order',
              'data' => 'Данные',
            ];
        }

        public function behaviors()
        {
            $behaviors = [
              'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
              ],
              'fileupload' => [
                'class' => \mirocow\fileinput\behaviors\UploadFileBehavior::className(),
                'provider' => 'mirocow\fileinput\providers\LocalStorage',
                'savePathAlias' => '@app/web/uploads',
                'publicPath' => 'uploads',
                'attributeName' => 'name',
                'multiple' => true,
                'fileTypes' => $this->fileTypes,
              ],
            ];

            return $behaviors;
        }

        /**
         * @return \yii\db\ActiveRelation
         */
        public function getEntity()
        {
            return $this->hasOne(SpecialMark::className(), ['id' => 'entity_id']);
        }

        public function beforeValidate()
        {

            return parent::beforeValidate();
        }

        public function beforeSave($insert)
        {

            if ($this->getIsNewRecord())
            {

                if (!Yii::$app->user->isGuest)
                {
                    // Заполняем
                    $this->user_id = Yii::$app->user->identity->id;
                }

            }

            return parent::beforeSave($insert);

        }

        public function afterSave($insert, $changedAttributes)
        {

            // Удаляем физическое расположение файла

            return parent::afterSave($insert, $changedAttributes);

        }

        public function afterDelete()
        {

            // Получаем путь к файлу
            $file_path = $this->behaviors['fileupload']->getSavePath();

            // Удаляем физическое расположение файла
            if (file_exists($file_path . $this->name))
            {

                @unlink($file_path . $this->name);

            }

            return parent::afterDelete();

        }

        public function uploaded()
        {
            if ($this->name !== '')
            {
                return $this->save(false);
            }

        }

        public function getFileName()
        {
            $file = [];
            $file[] = $this->path;
            $file[] = $this->name;
            return implode('', $file);
        }

    }
