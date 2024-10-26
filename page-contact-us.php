<?php
/*
Template Name: Contact Us
*/

get_header(); // 包含主题头部模板
?>

<p>Contact</p>

<?php
echo '<pre>';

print_r(get_queried_object());

echo '<pre/>';
?>