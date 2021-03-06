<?php

/**
 * The CategoryAPI class is a sample class.
 *
 * @category  CategoryAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class CategoryAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new Category.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string cat_name
     * @param string cat_desc
     * @param string cat_status
     * @param string car_currency
     * @param string cat_created
     * @param string cat_modified
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/categoryapi/save?
     */
    public function postSave($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $categoryModel=new CategoryModel();
        if (false == $categoryModel->validate($request_data))
        {
            throw new RestException(400, $categoryModel->error);
        }
        if (( ! isset($request_data['cat_name']) || ($request_data['cat_name'] == '')))
        {
            throw new RestException(400, 'please enter car name');
        }
        if (isset($request_data['cat_name']))
        {
            $result=$categoryModel->selectWhere(array('cat_name' => $request_data['cat_name']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Category Name Already Exists');
            }
        }
        //----------
        $request_data['cat_created']=date('Y-m-d H:i:s');
        $request_data['cat_modified']=date('Y-m-d H:i:s');
        $result=$categoryModel->insert($request_data);
        if ($result)
        {
            $result=$categoryModel->GetCategoryDetail(array('car_id' => $categoryModel->callback));
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['response']=$result;
            return $returnArr;
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Update category profile.
     *
     * @param string cat_name
     * @param string cat_desc
     * @param string cat_status
     * @param string car_currency
     * @param string cat_created
     * @param string cat_modified
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/categoryapi/updatecategoryprofile?
     */
    protected function postUpdateCategoryProfile($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $isSendEmail=1;
        $categoryModel=new CategoryModel();
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if (false == $categoryModel->validate($request_data))
        {
            throw new RestException(400, $categoryModel->error);
        }
        if (( ! isset($request_data['category_id']) || ($request_data['category_id'] == '')))
        {
            throw new RestException(400, 'Category Id Missing');
        }
        if (( ! isset($request_data['cat_name']) || ($request_data['cat_name'] == '')))
        {
            throw new RestException(400, 'Category Name Missing');
        }
        if (isset($request_data['cat_name']))
        {
            $result=$categoryModel->selectWhere(array('cat_name' => $request_data['cat_name']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Category Name Already Exists');
            }
        }
        //-------------------
        $categoryID=$request_data['category_id'];
        //--------------------
        $query="AND category_id !=$categoryID";
        //----------
        $request_data['cat_modified']=date('Y-m-d H:i:s');
        //-------
        unset($request_data['api_key']);
        //--------
        if ( ! empty($request_data))
        {
            $result=$categoryModel->update($request_data, "category_id='{$categoryID}'");
            if ($result)
            {
                $result=$categoryModel->GetCategoryDetail(array('category_id' => $categoryID));
                $returnArr['status']='OK';
                $returnArr['code']=200;
                $returnArr['message']=null;
                $returnArr['response']=$result;
                return $returnArr;
            }
            throw new RestException(400);
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Get all categories with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $category_id The category_id integer to set
     * @param integer $cat_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/categoryapi/getcategories?
     */
    protected function postGetCategories($request_data)
    {
        unset($request_data['api_key']);
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $categoryId=isset($request_data['category_id']) ? $request_data['category_id'] : NULL;
        $name=isset($request_data['cat_name']) ? $request_data['cat_name'] : NULL;
        $categoryModel=new CategoryModel();
        $response=$categoryModel->getCategories($categoryId, $name, $offset, $limit);
        if (( ! isset($response['result']) || ($response['result'] == '')))
        {
            throw new RestException(400, 'Invalid Category.');
        }
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=$response['next_offset'];
        $returnArr['last_offset']=$response['last_offset'];
        $returnArr['response']=$response['result'];
        return $returnArr;
    }

    function jschars($str)
    {
        $str=mb_ereg_replace("\\\\", "\\\\", $str);
        $str=mb_ereg_replace("\"", "\\\"", $str);
        //$str = mb_ereg_replace("'", "\\'", $str);
        $str=mb_ereg_replace("\r\n", "\\n", $str);
        $str=mb_ereg_replace("\r", "\\n", $str);
        $str=mb_ereg_replace("\n", "\\n", $str);
        $str=mb_ereg_replace("\t", "\\t", $str);
        $str=mb_ereg_replace("<", "\\x3C", $str); // for inclusion in HTML
        $str=mb_ereg_replace(">", "\\x3E", $str);
        return $str;
    }

}

?>
