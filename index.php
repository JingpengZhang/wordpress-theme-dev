<?php
get_header();
?>
<?php
$menu_tree2 = get_menu_tree('primary');
$page_url = get_current_url();
$page_data = get_queried_object();
echo '<pre>'; // 使输出格式更美观
// if(is_front_page()){
//   print_r('true');
// }else{
//   print_r('false');
// }

_e('title', 'zhongming');
print_r(get_the_terms($page_data->ID, 'product_cat'));
print_r($page_data);
print_r($menu_tree2);
// print_r(count(($menu_tree)));
echo '</pre>';
?>
</body>

</html>