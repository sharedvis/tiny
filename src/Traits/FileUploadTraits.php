<?php
/**
 * Send upload for curl traits
 *
 * @package Tiny
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

trait FileUploadTraits
{
    /**
     * Files that will be uploaded, structure must be like _FILES
     * @var array
     */
    protected $_files = [];

    /**
     * Is upload file
     * @var boolean
     */
    protected $_is_upload = false;

    /**
     * Add file manually to upload, valid value for $image is :
     * [
     *     'name' => 'image_name',
     *     'type' => 'mime_type',
     *     'tmp_name' => 'full_path_of_file',
     *     'size' => 'image_size'
     * ]
     *
     * @param string $name  Name of _FILES key
     * @param array  $value Contain information of images
     */
    public function addFile($name, $image = [])
    {
        $this->_files[$name] = $image;

        return $this;
    }

    /**
     * Get all files that will be uploaded
     * @return array
     */
    public function getFiles()
    {
        return array_merge($this->_files, $_FILES);
    }

    /**
     * Get all _FILES and convert it into cURLFile
     * @return array
     */
    protected function _alterFilesUpload($args)
    {
        if (!empty($files = $this->getFiles()))
        {
            $images = [];

            foreach ($files as $key => $value) {
                if (!empty($value['tmp_name'])) {
                    if (!is_array($value['tmp_name']))
                        $images[$key] = new \CurlFile($value['tmp_name'], $value['type'], $value['name']);
                    else {
                        foreach ($value['tmp_name'] as $order => $file) {
                            if (!empty($value['tmp_name'][$order])) {
                                $images[$key][$order] = new \CurlFile($value['tmp_name'][$order], $value['type'][$order], $value['name'][$order]);
                            }
                        }
                    }
                }
            }

            // Merge images to original data
            $args = array_merge($args, $images);

            // check if variable images is empty or not,
            // if empty then is not upload
            if(!empty($images)){
                $this->_is_upload = true;
            }
            else{
                $this->_is_upload = false;
            }
        }

        return $args;
    }
}
