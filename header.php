<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <?php wp_head(); ?>
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri() ?>">
</head>

<body>
  <div class="w-full bg-[#222] text-white h-20 flex px-12 justify-between items-center">
    <div class="h-full flex items-center w-44">
      <img class="h-[90%]" src="<?php echo base_on_theme_uri('/assets/logo.png'); ?>" />
    </div>

    <ul class="h-full flex gap-4">
      <?php
      $menu_tree = get_menu_tree('primary');
      echo '<pre>'; // 使输出格式更美观
      // print_r($menu_tree);
      // print_r(count(($menu_tree)));
      echo '</pre>';
      foreach ($menu_tree as $tree_level_1) {
      ?>
        <li
          class="
          <?php
          if (is_menu_item_active($tree_level_1)) {
          ?> 
            before:bg-primary text-[#333] 
            <?php
          } else {
            ?> 
              before:bg-transparent hover:before:bg-primary text-white hover:text-[#333] 
            <?php
          }
            ?> 
          group/item flex items-center relative before:absolute before:w-full before:h-full before:z-0 before:-skew-x-[25deg]">
          <a href="
            <?php echo get_translated_link($tree_level_1->url)  ?>
          " class="z-10 px-5 cursor-pointer h-full flex items-center font-bold">
            <?php echo $tree_level_1->title . $tree_level_1->object_id ?>
          </a>
          <?php if (count($tree_level_1->children) !== 0) { ?>
            <ul
              class="absolute h-0 group-hover/item:h-fit top-full left-0 bg-[#333] border-b-0 group-hover/item:border-b-2 border-primary overflow-hidden">
              <?php foreach ($tree_level_1->children as $tree_level_2) { ?>
                <li
                  class="border-b border-[#1f1f1f] relative after:absolute after:w-full [&:not(:last-child)]:after:h-px after:bg-primary after:-left-full hover:after:left-0 after:animate-[slideOutFromLeft_0.3s_linear] hover:after:animate-[slideInFromLeft_0.3s_linear] after:transition-all text-white hover:text-primary group/sub">
                  <a href="<?php echo $tree_level_2->url ?>"
                    class="flex items-center h-9 text-xs pl-0 group-hover/sub:pl-2 duration-300 text-nowrap w-40 cursor-pointer transition-all">
                    <i
                      class="border-x-0 border-y-0 group-hover/sub:border-y-[6px] mr-4 group-hover/sub:mr-0 group-hover/sub:border-x-8 duration-300 transition-all border-transparent border-l-primary"></i>
                    <?php echo $tree_level_2->title ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          <?php } ?>
        </li>
      <?php }
      ?>
    </ul>
    <div class="h-full flex items-center w-44 gap-8">
      <button>
        <span
          class="icon-[lucide--search]"
          style="width: 24px; height: 24px; color: white"></span>
      </button>

      <div class="relative">
        <button id="multi-language-switcher">
          <span
            class="icon-[iconoir--language]"
            style="width: 24px; height: 24px; color: white"></span>
        </button>
        <div
          id="multi-language-switcher-panel"
          class="absolute top-full right-0 bg-white shadow-md w-32 hidden">
          <ul class="text-[#333] text-xs w-full">
            <?php
            foreach (poccur_ml_get_selected_languages() as $language) {
            ?>
              <li
                onclick="switch_lang('<?php echo get_locale(); ?>','<?php echo  $language; ?>')"
                class="flex items-center hover:text-white cursor-pointer hover:bg-primary h-8 px-3 text-nowrap gap-3">
                <img src="<?php echo base_on_theme_uri('/assets/languages/en_US.png'); ?>" class="w-fit" />
                <span><?php _poccur_ml_e($language); ?></span>
              </li>
            <?php
            }
            ?>
          </ul>
        </div>

        <script>
          const languagesPanel = jQuery("#multi-language-switcher-panel");
          jQuery("#multi-language-switcher").click(() => {
            if (jQuery("#multi-language-switcher-panel").hasClass("hidden"))
              languagesPanel.removeClass("hidden");
          });
          jQuery(document).click((e) => {
            if (
              !languagesPanel.is(e.target) &&
              !jQuery("#multi-language-switcher").is(e.target) &&
              jQuery("#multi-language-switcher").has(e.target).length === 0 &&
              languagesPanel.has(e.target).length === 0
            ) {
              if (!languagesPanel.hasClass("hidden"))
                languagesPanel.addClass("hidden");
            }
          });

          const switch_lang = (currentLang, targetLang) => {
            jQuery.ajax({
              type: 'POST',
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              data: {
                action: 'switch_language',
                // 当前页面路径
                current_path: window.location.pathname,
                // 当前语言
                current_lang: currentLang,
                // 目标语言
                target_lang: targetLang,
              },
              success: function(response) {
                if (response.success) {
                  if (response.data.redirect_url) window.location.href = response.data.redirect_url;
                }
              },
              error: function() {
                jQuery('#response').html('AJAX request failed.'); // 处理错误
              }
            });
          }
        </script>
      </div>
    </div>
  </div>