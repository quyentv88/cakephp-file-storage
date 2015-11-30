<?php
/**
 * @author Florian Krämer
 * @copyright 2012 - 2015 Florian Krämer
 * @license MIT
 */
namespace Burzum\FileStorage\Storage\PathBuilder;

use Burzum\FileStorage\Storage\StorageManager;
use Cake\Datasource\EntityInterface;

class S3PathBuilder extends BasePathBuilder {

	public function __construct(array $config = []) {
		$this->_defaultConfig['https'] = false;
		$this->_defaultConfig['modelFolder'] = true;
		$this->_defaultConfig['s3Url'] = 's3.amazonaws.com';
		parent::__construct($config);
	}

	protected function _getBucket($adapter) {
		$config = StorageManager::config($adapter);
		return $config['adapterOptions'][1];
	}

	protected function _buildCloudUrl($bucket, $bucketPrefix = null, $cfDist = null) {
		$path = $this->config('https') === true ? 'https://' : 'http://';
		if ($cfDist) {
			$path .= $cfDist;
		} else {
			if ($bucketPrefix) {
				$path .= $bucket . '.' . $this->_config['s3Url'];
			} else {
				$path .= $this->_config['s3Url'] . '/' . $bucket;
			}
		}
		return $path;
	}

	/**
	 * Builds the URL under which the file is accessible.
	 *
	 * This is for example important for S3 and Dropbox but also the Local adapter
	 * if you symlink a folder to your webroot and allow direct access to a file.
	 *
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param array $options
	 * @return string
	 */
	public function url(EntityInterface $entity, array $options = []) {
		$bucket = $this->_getBucket($entity->adapter);
		$pathPrefix = $this->_buildCloudUrl($bucket);
		$path = parent::path($entity);
		$path = str_replace('\\', '/', $path);
		return $pathPrefix . $path;
	}
}