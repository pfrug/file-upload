<?php

namespace Pfrug\FileUpload\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Author: P.Frugone <frugone@gmail.com>
 * Date: 23/04/2019
 */
trait HasImageUploads{

	/**
	 * Path to upload folder
	 * @var string
	 */
	protected $imagesUploadPath = '';

	/**
	 * Driver
	 * @var string {local|public|s3|...}
	 */
	protected $storageDisk = 'public';

	/**
	 * Indicates whether to delete images prefixed with $prefixToDelete
	 * @var boolean
	 */
	protected $deleteIfPrefix = true;

	/**
	 * Prefix used to mark an image to delete
	 * @var string
	 */
	protected $prefixToDelete = 'delete_';

	public static function bootHasImageUploads()
	{
		static::saved(function($model) {
			$model->saveImages();
		});

		static::deleted(function($model) {
			$model->deleteStorageImages();
		});
	}

	/**
	 * Get the Url of the image
	 * @param string $fieldName
	 */
	public function getImageUrl($fieldName)
	{
		if ($this->getOriginal($fieldName)) {
			return $this->getStorageDisk()->url($this->getOriginal($fieldName));
		} else {
			return '';
		}
	}

	/**
	 * Remove image from registry and storage
	 * @param string $fieldName
	 */
	public function deleteImage($fieldName){
		if (in_array( $fieldName, $this->getUploadFields())) {
			$imagePath = $this->getOriginal($fieldName);
			if ($imagePath) {
				$this->deleteStorageImage($imagePath);
				$this->updateModel(null ,$fieldName);
			}
		}
	}

	/**
	 * Save images in storage
	 */
	private function saveImages(){
		foreach ($this->getUploadFields() as $fieldName ) {
			if (request()->hasFile($fieldName)) {
				$this->uploadImage($fieldName);
			} else if ($this->deleteIfPrefix && request()->get($this->prefixToDelete.$fieldName) == 1) {
				$this->deleteImage($fieldName);
			}
		}
	}

	/**
	 * Remove all saved images for this model
	 */
	private function deleteStorageImages()
	{
		foreach ($this->getUploadFields() as $fieldName)
		{
			$imagePath = $this->getOriginal($fieldName);
			$this->deleteStorageImage($this->getOriginal($fieldName));
		}
	}

	/**
	 * Removes the specified image from storage
	 * @param string $filePath
	 */
	private function deleteStorageImage($filePath)
	{
		if ($filePath && $this->getStorageDisk()->exists($filePath)) {
			$this->getStorageDisk()->delete($filePath);
		}
	}

	/**
	 * Save image in storage
	 * Remove old image.
	 * Update filepath in db
	 * @param string $fieldName
	 */
	public function uploadImage($fieldName)
	{
  		$file = request()->file($fieldName);

		$currentFile = $this->getOriginal($fieldName);

   		$filePath = $this->getUploadPath($file, $fieldName);

		$this->saveImage($file, $filePath);

		$this->updateModel($filePath, $fieldName );

		if ($currentFile != $filePath) {
			$this->deleteStorageImage($currentFile);
		}
	}

	/**
	 * save image in storage
	 * @param Illuminate\Http\UploadedFile $image
	 * @param string $filePath
	 */
 	protected function saveImage($image, $filePath) {
		$this->getStorageDisk()->put(
			$filePath,
			file_get_contents($image->getRealPath())
		);
	}

	/**
	 * Get the FilesystemAdapter for the $this->storageDisk
	 * @return Illuminate\Filesystem\FilesystemAdapter
	 */
	private function getStorageDisk()
	{
		return Storage::disk($this->storageDisk);
	}

	/**
	 * Update model value
	 * @param string $value
	 * @param string $fieldName
	 */
	protected function updateModel($value, $fieldName)
	{
		$this->attributes[$fieldName] = $value;
		$dispatcher = $this->getEventDispatcher();
		self::unsetEventDispatcher();
		$this->save();
		self::setEventDispatcher($dispatcher);
	}

	/**
	 * Returns the path where the file should be saved
	 * @param Illuminate\Http\UploadedFile $image
	 * @param string $fileName
	 * @return string
	 */
	protected function getUploadPath($file, $fieldName) {
   		$upladoDir = $this->getUploadDir($fieldName);
		$uploadName = $this->getUploadName($file, $fieldName);
   		return $upladoDir.$uploadName;
	}

	/**
	 * Returns the directory to upload files
	 * @param string $fieldName
	 * @return string
	 */
	protected function getUploadDir($fieldName = '')
	{
		return $this->imagesUploadPath.$this->getFolderToUpload().DIRECTORY_SEPARATOR ;
	}

	/**
	 * Returns the name with which the file will be saved
	 * @return string
	 */
	protected function getUploadName($file, $fieldName)
	{
		return md5($this->getKey() . '_' . $fieldName . '_' . microtime() ) . '.' . $file->extension();
	}

	/**
	 * Returns the folder where the file will be saved
	 * @return string
	 */
	private function getFolderToUpload()
	{
		return (isset($this->model->uploadFolderName))
			? $this->model->uploadFolderName
			: strtolower(Str::plural((new \ReflectionClass(get_class($this)))->getShortName()));
	}

	/**
	 * Returns the list of fields of type file
	 * @return array
	 */
	private function getUploadFields()
	{
		return @is_array($this->uploadableImages) ? $this->uploadableImages:[];
	}
}