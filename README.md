FileInput Widget for Yii2
==============================

Renders a [Jasny File Input Bootstrap](http://jasny.github.io/bootstrap/javascript/#fileinput) widget.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
php composer.phar require "mirocow/yii2-file-input-widget" "*"
```

or add

```json
"mirocow/yii2-file-input-widget" : "*"
```

to the require section of your application's `composer.json` file.

Usage
-----

Using a model:
==============

```php
    public function behaviors() {
        $behaviors = [          
            'fileupload' => [
                'class' => 'mirocow\fileinput\behaviors\UploadFileBehavior',
                'attributeName' => 'file'
            ],            
        ];
        
        return $behaviors;
    }
```

or use LocalStorage provider

```php
    public function behaviors() {
        $behaviors = [          
            'fileupload' => [
                'class' => 'mirocow\fileinput\behaviors\UploadFileBehavior',
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
```
    
or use Uploads.Im Storage provider

```php
    public function behaviors() {
        $behaviors = [          
            'fileupload' => [
                'class' => 'mirocow\fileinput\behaviors\UploadFileBehavior',
                'provider' => 'mirocow\fileinput\providers\UploadsImStorage',
                'savePathAlias' => '@app/web/uploads',
                'publicPath' => 'uploads',
                'attributeName' => 'name',
                'multiple' => true,
                'fileTypes' => $this->fileTypes,
            ],            
        ];
        
        return $behaviors;
    }
```        

or extendig model File

Example:
```php
<?php

    namespace app\modules\core\models;

    use mirocow\fileinput\models\File;

    class Image extends File
    {


    }
```

Using a view:
==============

Example 1:
```php
use mirocow\fileinput\FileInput;

<?=FileInput::widget([
    'model' => $model,
    'name' => 'Image[name][]', // image is the attribute
    // using STYLE_IMAGE allows me to display an image. Cool to display previously
    // uploaded images
    'thumbnail' => $model->getThumbnailUrl(),
    'style' => FileInput::STYLE_IMAGE
]);?>
```
Example 2:
```php
<?= FileInput::widget([
  'name' => 'Image[name][]',
  'style' => FileInput::STYLE_INPUT,
  //'style' => FileInput::STYLE_CUSTOM,
  //'customView' => __DIR__ . '/widgets/file_input.php',
  'addMoreButton' => true,
  'buttonCaption' => 'Дбавить еще',
])?>
```

Using a view: JQuery Ajax Upload
==============

Js file
```js
$('#my-form form').submit(function(){

    var form = new FormData();

    $.each($('#data-form :input'), function(i, file) {
        var field_name = this.name;

        if(this.name) {
          if ($(this)[0].files) {
            $.each($(this)[0].files, function (i, file) {
              if(file.size) {
                form.append(field_name, file);
              }
            });
          } else {
            var v = $(this).val();
            if(v) {
              form.append(field_name, v);
            }
          }
        }
    });

    $.ajax({
        type: "POST",
        url: path,
        data: form,
        cache: false,
        contentType: false,
        processData: false

    }).done(function( msg ) {

      // something...

    }).fail(function(msg){
        
      // Error  
        
    });

    return false;
});
```
Html template form
```html
<form class="form-horizontal" enctype="multipart/form-data" id="data-form">

    <div class="col-xs-8">

        <div class="form-group">
            <textarea name="Order[comment]" class="form-control" rows="3" placeholder="Commet"></textarea>

            <br>

            <?= FileInput::widget([
              'name' => 'Image[name][]',
              'style' => FileInput::STYLE_INPUT,
              //'style' => FileInput::STYLE_CUSTOM,
              //'customView' => __DIR__ . '/widgets/file_input.php',
              'addMoreButton' => true,
              'buttonCaption' => 'Add more
            ])?>

            <button type="submit" class="btn btn-success btn-sm2">Submit</button>
        </div>
    </div>
</form>
```                    

Sql
=================
```sql
CREATE TABLE `tbl_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID профиля',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Файл',
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT 'Путь до файла',
  `create_time` datetime DEFAULT NULL COMMENT 'Дата создания',
  `update_time` datetime DEFAULT NULL COMMENT 'Дата обновления',
  `entity_type` varchar(255) NOT NULL DEFAULT '' COMMENT 'Тип сущности',
  `entity_id` int(11) NOT NULL COMMENT 'ID сущности (Анкета, Запрос, Марка итд)',
  `mime_type` varchar(20) NOT NULL COMMENT 'Mime тип',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Сортировка',
  `data` text COMMENT 'Данные',
  PRIMARY KEY (`id`),
  KEY `fk_file_special_mark_id` (`entity_id`),
  KEY `fk_file_user_id` (`user_id`),
  CONSTRAINT `fk_file_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
```

