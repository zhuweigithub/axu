<?php
/**
 * 图片处理类
 * Created by zw.
 * User: zw
 * email 452436132@qq.com
 * Date: 2016/10/15
 * Time: 21:30
 */
namespace Home\Controller;

use Think\Controller;
use Think\Exception;

class ImgDisposeController extends FatherController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 图片链接
	 * @param int $image_url
	 * @param int $width 宽度
	 * @param int $height 高度
	 * @return string
	 */
	public function imgBuildSize($image_url = 0, $width = 100, $height = 100)
	{
		$image_url       = "Public/img/upload/2016_10_14/3.jpg";
		$width           = 100;
		$height          = 100;
		$gdImageResource = $this->image_create_from_ext($image_url);
		$originalWidth   = imagesx($gdImageResource);
		$originalHeight  = imagesy($gdImageResource);
		$dst_im          = imagecreatetruecolor($width, $height);
		imagecopyresized($dst_im, $gdImageResource, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
		imagejpeg($dst_im);
		imagedestroy($dst_im);
		imagedestroy($gdImageResource);
		header('Content-type: image/jpeg');
	}

	public function delSourceImg()
	{

	}


	/** 图片高斯模糊（适用于png/jpg/gif格式）   $srcImg 原图片  $savePath 保存路径  $saveName 保存名字   $blurFactor 模糊程度 int
	 * @return string
	 * @throws \Think\Exception
	 */
	public function imgFuzzy()
	{
		/*	$srcImg     = "Public/img/upload/2016_10_14/3.jpg";
			$savePath   = "Public/img/upload/2016_10_14";
			$saveName   = "4.png";
			$blurFactor = 2;*/
		$srcImg          = $this->getRequestParam('srcImg');
		$savePath        = $this->getRequestParam('savePath');
		$saveName        = $this->getRequestParam('saveName');
		$blurFactor      = $this->getRequestParam('blurFactor');
		$gdImageResource = $this->image_create_from_ext($srcImg);
		$srcImgObj       = $this->blur($gdImageResource, $blurFactor);
		$temp            = pathinfo($srcImg);
		$name            = $temp['basename'];
		$path            = $temp['dirName'];
		$saveName        = $saveName ? $saveName : $name;
		$savePath        = $savePath ? $savePath : $path;
		$saveFile        = $savePath . '/' . $saveName;
		$srcInfo         = @getimagesize($srcImg);
		switch ($srcInfo[2]) {
			case 1:
				imagegif($srcImgObj, $saveFile);
				break;
			case 2:
				imagejpeg($srcImgObj, $saveFile);
				break;
			case 3:
				imagepng($srcImgObj, $saveFile);
				break;
			default:
				return '保存失败'; //保存失败
		}

		return $saveFile;
	}

	/**
	 * Strong Blur
	 * @param  $gdImageResource 图片资源
	 * @param  $blurFactor 可选择的模糊程度
	 *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
	 * @return GD image 图片资源类型
	 * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
	 */
	private function blur($gdImageResource, $blurFactor = 0)
	{
		$blurFactor     = round($blurFactor);
		$originalWidth  = imagesx($gdImageResource);
		$originalHeight = imagesy($gdImageResource);
		$smallestWidth  = ceil($originalWidth * pow(0.5, $blurFactor));
		$smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));
		$prevImage      = $gdImageResource;
		$prevWidth      = $originalWidth;
		$prevHeight     = $originalHeight;
		for ($i = 0; $i < $blurFactor; $i += 1) {
			$nextWidth  = $smallestWidth * pow(2, $i);
			$nextHeight = $smallestHeight * pow(2, $i);
			$nextImage  = imagecreatetruecolor($nextWidth, $nextHeight);
			imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
				$nextWidth, $nextHeight, $prevWidth, $prevHeight);
			imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);
			$prevImage  = $nextImage;
			$prevWidth  = $nextWidth;
			$prevHeight = $nextHeight;
		}
		imagecopyresized($gdImageResource, $nextImage,
			0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
		imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);
		imagedestroy($prevImage);
		return $gdImageResource;
	}


	/**  区分图片类型
	 * @param $imgFile
	 * @return null|resource
	 */
	private function image_create_from_ext($imgFile)
	{
		$info = getimagesize($imgFile);
		$im   = null;
		switch ($info[2]) {
			case 1:
				$im = imagecreatefromgif($imgFile);
				break;
			case 2:
				$im = imagecreatefromjpeg($imgFile);
				break;
			case 3:
				$im = imagecreatefrompng($imgFile);
				break;
		}
		return $im;
	}

}