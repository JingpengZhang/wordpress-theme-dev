<?php

add_filter('show_admin_bar', '__return_false');

function enqueue_jquery()
{
  wp_enqueue_script('jquery'); // 加载 WordPress 自带的 jQuery
}
add_action('wp_enqueue_scripts', 'enqueue_jquery');

register_nav_menus(array(
  "primary" => "主导航菜单",
  "footer" => "页脚菜单",
  "menus1" => '自定义菜单位置1',
  "menus2" => '自定义菜单位置2'
));

add_action('woocommerce_product_options_general_product_data', 'add_custom_language_fields');

function add_custom_language_fields()
{
  // 添加英文标题字段
  woocommerce_wp_text_input(array(
    'id' => 'custom_product_title_en',
    'label' => __('Product Title (EN)', 'your-theme'),
    'desc_tip' => 'true',
    'description' => __('Enter the product title in English.', 'your-theme'),
  ));

  // 添加中文标题字段
  woocommerce_wp_text_input(array(
    'id' => 'custom_product_title_zh',
    'label' => __('Product Title (ZH)', 'your-theme'),
    'desc_tip' => 'true',
    'description' => __('Enter the product title in Chinese.', 'your-theme'),
  ));
}

add_action('woocommerce_process_product_meta', 'save_custom_language_fields');


function save_custom_language_fields($post_id)
{
  $custom_title_en = isset($_POST['custom_product_title_en']) ? sanitize_text_field($_POST['custom_product_title_en']) : '';
  $custom_title_zh = isset($_POST['custom_product_title_zh']) ? sanitize_text_field($_POST['custom_product_title_zh']) : '';

  update_post_meta($post_id, 'custom_product_title_en', $custom_title_en);
  update_post_meta($post_id, 'custom_product_title_zh', $custom_title_zh);
}



/**
 * 获取指定菜单的树形结构菜单项
 *
 * @param string $menu_name 菜单名称
 * @return array 菜单项的树形结构
 */
function get_menu_tree($menu_name)
{
  // 获取菜单位置
  $menu_locations = get_nav_menu_locations();

  // 如果指定的菜单不存在，返回空数组
  if (!isset($menu_locations[$menu_name])) {
    return array();
  }

  // 获取菜单 ID
  $menu_id = $menu_locations[$menu_name];

  // 获取菜单项
  $menu_items = wp_get_nav_menu_items($menu_id);

  // 创建一个关联数组，方便查找每个菜单项
  $menu_lookup = array();
  $menu_structure = array();

  foreach ($menu_items as $item) {
    // 将每个菜单项加入查找数组
    $menu_lookup[$item->ID] = (object) array_merge(
      get_object_vars($item),
      array(
        'children' => array(), // 用于存放子菜单项
      )
    );
  }

  // 构建树形结构，确定父子关系
  foreach ($menu_items as $item) {
    if ($item->menu_item_parent == 0) {
      // 如果是顶级菜单项，直接添加到结构中
      $menu_structure[] = $menu_lookup[$item->ID];
    } else {
      // 如果是子菜单项，添加到对应的父菜单项中
      $menu_lookup[$item->menu_item_parent]->children[] = $menu_lookup[$item->ID];
    }
  }

  return $menu_structure; // 返回结构
}


function is_menu_item_active($menu_item)
{
  // 当前页面数据
  $page_data = get_queried_object();

  // 如果是页面
  if ($menu_item->object == 'page') {
    // 获取页面模板
    $page_template = get_page_template_slug($menu_item->object_id);

    if ($menu_item->object_id == $page_data->ID) {
      return true;
    } else {
      // 如果是产品总页
      if ($page_template == 'page-product.php') {
        // 如果是产品页或产品分类页，都高亮 
        if ($page_data->taxonomy == 'product_cat' || is_product()) {
          return true;
        }
      } else {
        // 如果是产品分类页面
        if ($page_data->taxonomy == 'product_cat' && is_product()) {
          $product_cat_arr = get_the_terms($page_data->ID, 'product_cat');
          $target_id = $menu_item->object_id;
          if (!empty(array_filter($product_cat_arr, function ($obj) use ($target_id) {
            return isset($obj->term_id) && $obj->term_id == $target_id;
          }))) {
            return true;
          }
        }
      }
    }
  }

  return false;
}

/**
 * 检查菜单项是否为当前页面或其祖先菜单项
 *
 * @param array $menu_tree 菜单树结构
 * @param object $current_menu_item 当前菜单项对象
 * @return bool
 */
function is_current_or_ancestor_menu_item($menu_tree, $current_menu_item)
{
  // 获取当前菜单项的 URL
  $current_url = esc_url($current_menu_item->url); // 当前菜单项的 URL
  $home_url = home_url(); // 获取当前网站的首页 URL

  // 递归检查菜单项
  return check_menu_item($menu_tree, $current_url, $home_url);
}

/**
 * 获取当前页面的完整 URL
 *
 * @return string 当前页面的完整 URL
 */
function get_current_url()
{
  // 获取协议
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
  // 获取主机名
  $host = $_SERVER['HTTP_HOST'];
  // 获取请求的 URI
  $uri = $_SERVER['REQUEST_URI'];

  // 返回完整的 URL
  return $protocol . $host . $uri;
}


/**
 * 递归检查菜单项
 *
 * @param array $menu_items 菜单项数组
 * @param string $current_url 当前菜单项的完整 URL
 * @param string $home_url 当前网站的首页完整 URL
 * @return bool
 */
function check_menu_item($menu_items, $current_url, $home_url)
{
  foreach ($menu_items as $item) {
    // 获取菜单项的完整 URL
    $page_url = get_current_url();

    // 检查当前菜单项的 URL 是否与当前页面 URL 匹配
    if ($page_url === $current_url) {
      return true; // 找到匹配
    }

    // 特殊处理首页情况
    if ($current_url === $home_url) {
      // 仅当当前页面是首页时，检查是否有对应的首页菜单项
      if ($page_url === $home_url) {
        return true; // 找到指向首页的菜单项
      }
    }

    // 递归检查子菜单项
    if (!empty($item->children)) {
      if (check_menu_item($item->children, $current_url, $home_url)) {
        return true; // 找到匹配在子菜单中
      }
    }
  }

  return false; // 没有找到匹配
}

// 引入资源
function base_on_theme_uri($url)
{
  return esc_url(get_template_directory_uri() . $url);
}
