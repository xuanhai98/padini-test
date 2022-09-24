<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


class Amasty_Xsearch_Helper_Category extends Amasty_Xsearch_Helper_Abstract
{
    const CODE = 'categories';

    protected function getCode()
    {
        return self::CODE;
    }

    /**
     * Search categories.
     *
     * @param  string $keyword
     * @param  int $limit
     * @return array
     */
    public function search($keyword, $limit)
    {
        $result = array(
            'title' => $this->__('Categories'),
            'items' => array()
        );

        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        foreach ($this->getCollection($keyword, $limit) as $category) {
            if ($category->getRequestPath() !== null) {
                $name = $this->getBreadcrumbs($category);
                $result['items'][] = array(
                    'name' => $name,
                    'link' => $baseUrl . $category->getRequestPath()
                );
            }
        }

        return $result;
    }

    /**
     * @param $keyword
     * @param $limit
     * @return mixed
     */
    public function getCollection($keyword, $limit)
    {
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addNameToResult()
            ->addIsActiveFilter()
            ->addAttributeToFilter('name', array('like' => '%' . $keyword . '%'))
            ->addUrlRewriteToResult();

        $categories->getSelect()->limit($limit);

        return $categories;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    private function getBreadcrumbs($category)
    {
        $breadcrumbs = '';
        $parentIds = explode('/', $category->getPath());
        $parentCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addNameToResult()
            ->addFieldToFilter('entity_id', array('in' => $parentIds));
        $parentCollection->load();
        foreach ($parentIds as $parentId) {
            $parentCategory = $parentCollection->getItemById($parentId);
            if ($parentCategory && $parentCategory->getRequestPath() !== null) {
                $breadcrumbs .= ' > ' . $parentCategory->getName();
            }
        }
        $breadcrumbs = ltrim($breadcrumbs, ' > ');

        return $breadcrumbs;
    }
}
