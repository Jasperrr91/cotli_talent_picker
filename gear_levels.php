<?php
include "navigation.php";
$game_defines = new GameDefines();
$game_json = $game_defines->game_json;
if (!empty($_POST['user_id']) && !empty($_POST['user_hash']) || !empty($_POST['raw_user_data'])) {
  $user_info = new UserDefines('', $_POST['user_id'], $_POST['user_hash'], $_POST['raw_user_data']);
  $user_crusaders = '<table style="float: left; clear:both;"><tr>';
  $gear_level = $_POST['gear_level'];
  $column_count = 0;
  foreach ($user_info->crusaders AS $id => $crusader) {
    $crusader_name = '';
    if ($crusader->owned == 1) {
      $crusader_loot = get_crusader_loot($game_defines->crusaders[$crusader->hero_id], $user_info->loot, $game_defines->crusader_loot, $game_defines->loot);
      $crusader_loot_html[1] = $crusader_loot[1];
      $crusader_loot_html[2] = $crusader_loot[2];
      $crusader_loot_html[3] = $crusader_loot[3];
      $allowed_crusader = false;
      foreach ($crusader_loot AS $loot) {
        if (is_numeric($loot) && $loot == $gear_level) {
          $allowed_crusader = true;
          break;
        }
      }
      if ($allowed_crusader === false) {
        continue;
      }
      $crusader_image_info = get_crusader_image($game_defines->crusaders[$crusader->hero_id]->name);
      $image = $crusader_image_info['image'];
      $crusader_name = $crusader_image_info['name'];
      if ($column_count > 10) {
        $user_crusaders .= '</tr></tr>';
        $column_count = 0;
      }
      $user_crusaders .= '<td style="height: 48px; width: 48px;background-repeat: no-repeat; background-size: contain;background-image: url(\'' . $image .'\')">' . $crusader_name . '</td><td>' . implode('', $crusader_loot_html) . '</td>';
      $column_count++;
    }
  }
  $user_crusaders .= '</tr></table>';
}

//Copy pasta from user_profiles.php with added stuff, should probably be refactored
function get_crusader_loot($crusader, $user_loot, $all_crusader_loot, $all_loot) {
  $owned_crusader_gear = array(1 => '<div style="background-color: black;">N</div>',
                               2 => '<div style="background-color: black;">N</div>',
                               3 => '<div style="background-color: black;">N</div>',
                               4 => 0,
                               5 => 0,
                               6 => 0);
  foreach ($user_loot AS $id => $loot) {
    if ($all_loot[$loot->loot_id]->hero_id == $crusader->id) {
      foreach($all_crusader_loot[$all_loot[$loot->loot_id]->hero_id] AS $slot_id => $crusader_all_slot_loot) {
        foreach ($crusader_all_slot_loot AS $crusader_slot_loot) {
          if ($crusader_slot_loot->id == $loot->loot_id) {
            $gear_level = '';
            switch ($crusader_slot_loot->rarity) {
              case 1:
                  $gear_level = '<div style="background-color: grey;">C</div>';
                break;
              case 2:
                  $gear_level = '<div style="background-color: green;">U</div>';
                break;
              case 3:
                  $gear_level = '<div style="background-color: #3378fe;">R</div>';
                break;
              case 4:
                if ($crusader_slot_loot->golden == 0) {
                  $gear_level = '<div style="background-color: mediumpurple;">E</div>';
                } else {
                  $gear_level = '<div style="background-color: lightcoral;">GE</div>';
                }
                break;
              case 5:
                if ($crusader_slot_loot->golden == 0) {
                  $gear_level = '<div style="background-color: lightblue;">' . $loot->count . '</div>';
                } else {
                  $gear_level = '<div style="background-color: gold;">' . $loot->count . '</div>';
                }
                $owned_crusader_gear[$crusader_slot_loot->slot_id + 3] = $loot->count;
                break;
            }
            $owned_crusader_gear[$crusader_slot_loot->slot_id] = $gear_level;
          }
        }
      }
    }
  }
  return $owned_crusader_gear;
}

?>
<div style="color:red;">This will only display your crusaders with legendary gear at the level of your choosing</div>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post">
<div style="float: left;padding-right: 5px; clear: left;">
  User Id: <input type="text" name="user_id" value="<?php echo (isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : ''); ?>"><br>
  User Hash: <input type="text" name="user_hash" value="<?php echo (isset($_POST['user_hash']) ? htmlspecialchars($_POST['user_hash']) : ''); ?>"><br>
  Raw User Data: <input type="text" name="raw_user_data" value="<?php echo (isset($_POST['raw_user_data']) ? htmlspecialchars($_POST['raw_user_data']) : ''); ?>"><br>
  Gear Level: <input type="text" name="gear_level" value="<?php echo (isset($_POST['gear_level']) ? htmlspecialchars($_POST['gear_level']) : ''); ?>"><br>
</div>
<input style="clear:both; float: left;" type="submit">
</form>
<?php
if (!empty($user_crusaders)) {
  echo $user_crusaders;
}
?>
</html>