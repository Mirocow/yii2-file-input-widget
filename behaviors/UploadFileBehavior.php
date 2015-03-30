<?php

    namespace mirocow\fileinput\behaviors;

    use yii;
    use yii\base\Behavior;
    use yii\web\UploadedFile;
    use yii\db\ActiveRecord;
    use yii\validators\Validator;

    class UploadFileBehavior extends Behavior
    {

        public $provider = null;

        public function __construct($config = [])
        {
            if (!empty($config['provider']))
            {
                $class = Yii::getAlias($config['provider']);
                $this->provider = new $class;
                foreach ($config as $field_name => $value)
                {
                    if (isset($this->provider->{$field_name}))
                    {
                        $this->provider->{$field_name} = $value;
                    }
                }

                $this->provider->init();

            } else
            {
                throw new \HttpException(500, "Data storage provider does not exists.");
            }
        }

        public function __get($name)
        {

            if (isset($this->provider->{$name}))
            {

                return $this->provider->{$name};

            }

        }

        public function __set($name, $params)
        {

            if (isset($this->provider->{$name}))
            {

                $this->provider->{$name} = $params;

            }

        }

        public function __call($method, $params)
        {

            if (method_exists($this->provider, $method))
            {

                call_user_func_array([$this->provider, $method], [$params]);

            }

        }

        public function events()
        {
            $events = [];

            if (!$this->multiple)
            {
                $events = [
                  ActiveRecord::EVENT_BEFORE_INSERT => 'uploadFiles'
                  //ActiveRecord::EVENT_BEFORE_VALIDATE=> 'uploadFile'
                ];
            }

            return $events;
        }

        public function attach($owner)
        {
            parent::attach($owner);

            if (in_array($owner->scenario, $this->scenarios))
            {
                $fileValidator = Validator::createValidator(
                  'file',
                  $this->owner,
                  $this->provider->attributeName,
                  ['types' => $this->provider->fileTypes]
                );

                $owner->validators[] = $fileValidator;
            }
        }

        public function uploadFiles($event)
        {
            if (in_array($this->owner->scenario, $this->scenarios))
            {

                // Check multiple upload
                if ($this->provider->multiple)
                {
                    $files = UploadedFile::getInstances($this->owner, $this->provider->attributeName);
                } else
                {
                    $files[] = UploadedFile::getInstance($this->owner, $this->provider->attributeName);
                }

                if (!empty($this->owner->attributes['entity_type']))
                {
                    $entity_type = $this->owner->attributes['entity_type'];
                } else
                {
                    $entity_type = get_class($this->owner);
                }

                $entity_id = $this->owner->attributes['entity_id'];

                if(!$entity_id)
                    throw new \HttpException(500, "Entity ID of data storage provider does not exists.");

                $user_id = $this->owner->attributes['user_id'];

                $class = get_class($this->owner);

                foreach ($files as $index => $file)
                {

                    if (!$file)
                    {
                        continue;
                    }

                    if ($newFileName = $this->provider->save($file))
                    {

                        $file_item = $class::find()->where(
                          [
                            'entity_type' => $entity_type,
                            'entity_id' => $entity_id,
                            'original_name' => $file->name,
                          ]
                        )->one();

                        if (!$file_item)
                        {
                            $file_item = new $class;
                        }

                        $file_item->name = $newFileName;
                        $file_item->original_name = $file->name;
                        $file_item->mime_type = $file->type;
                        $file_item->entity_type = $entity_type;
                        $file_item->entity_id = $entity_id;
                        $file_item->user_id = $user_id? $user_id: Yii::$app->user->id;
                        $file_item->path = $this->getPublicPath();
                        $file_item->order = $index;
                        $file_item->save(false);

                    }

                }

            }
            return $files;
        }


        public function getPublicPath()
        {
            if (!empty($this->owner->path))
            {
                return $this->owner->path;
            } else
            {
                return $this->provider->getPublicPath();
            }
        }

    }