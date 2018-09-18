<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use HelperImageUploader;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CategoryMenuThumbnailUploader
 */
final class CategoryMenuThumbnailUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ImageUploadException
     */
    public function upload($categoryId, UploadedFile $uploadedImage)
    {
        //Get total of image already present in directory
        $files = scandir(_PS_CAT_IMG_DIR_, SCANDIR_SORT_NONE);
        $usedKeys = [];
        $allowedKeys = [0, 1, 2];

        foreach ($files as $file) {
            $matches = [];

            if (preg_match('/^' . $categoryId . '-([0-9])?_thumb.jpg/i', $file, $matches) === 1) {
                $usedKeys[] = (int) $matches[1];
            }
        }

        $availableKeys = array_diff($allowedKeys, $usedKeys);
        $helper = new HelperImageUploader('thumbnail');
        $uploadedFiles = $helper->process();
        $total_errors = [];

        if (count($availableKeys) < count($files)) {
            //@todo: throw exception
        }

        foreach ($uploadedFiles as &$uploadedFile) {
            $key = array_shift($availableKeys);
            $errors = array();

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (isset($uploadedFile['save_path']) && !ImageManager::checkImageMemoryLimit($uploadedFile['save_path'])) {
                $errors[] = $this->trans('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ', array(), 'Admin.Notifications.Error');
            }

            // Copy new image
            if (!isset($uploadedFile['save_path'])
                || (
                    empty($errors)
                    && !ImageManager::resize($uploadedFile['save_path'], _PS_CAT_IMG_DIR_ . $categoryId . '-' . $key . '_thumb.jpg')
                )
            ) {
                throw new ImageUploadException('An error occurred while uploading the image.');
            }

            if (count($errors)) {
                $total_errors = array_merge($total_errors, $errors);
            }

            if (isset($uploadedFile['save_path']) && is_file($uploadedFile['save_path'])) {
                unlink($uploadedFile['save_path']);
            }

            //Necesary to prevent hacking
            if (isset($uploadedFile['save_path'])) {
                unset($uploadedFile['save_path']);
            }

            if (isset($uploadedFile['tmp_name'])) {
                unset($uploadedFile['tmp_name']);
            }
        }

        \Tools::clearSmartyCache();
    }
}
