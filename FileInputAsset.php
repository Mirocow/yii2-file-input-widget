<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace mirocow\fileinput;

use yii\web\AssetBundle;

/**
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\yii2\widgets
 */
class FileInputAsset extends AssetBundle
{
	public $sourcePath = '@vendor/mirocow/yii2-file-input-widget/assets';

	public $js = [
		'js/fileinput.js',
        'js/addMoreButton.js'
	];

	public $css = [
		'css/fileinput.css'
	];

	public $depends = [
		//'yii\bootstrap\BootstrapPluginAsset'
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
	];

}