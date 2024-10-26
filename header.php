<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri() ?>">
</head>

<body>
  <div class="w-full bg-[#222] text-white h-16 flex px-8 justify-between items-center">
    <div class="">

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
            <?php echo $tree_level_1->url ?>
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
    <div></div>
  </div>