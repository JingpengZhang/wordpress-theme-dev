<?php
/*
Template Name: Product
*/

get_header(); // 包含主题头部模板

?>
<p>product</p>

<pre>
<?php
print_r(get_locale());
_poccur_ml_e('fr');
print_r(poccur_ml_get_selected_languages());
?>

</pre>