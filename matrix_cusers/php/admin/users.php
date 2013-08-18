<?php

if (!empty($_POST['add'])) {
  $validate = $this->validate('register');
  
  // success message
  if ($validate) {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/USER_ADDSUCCESS'), true);
  }
  
  // error message
  else {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/USER_ADDERROR'), false);
  }
  
  // refresh the index to reflect the changes
  $this->matrix->refreshIndex();
}

$users = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' ORDER BY id ASC');

?>

<h3 class="floated"><?php echo i18n_r(self::FILE.'/USERS'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>&smilies"><?php echo i18n_r(self::FILE.'/SMILIES'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&config"><?php echo i18n_r(self::FILE.'/CONFIG'); ?></a>
  <a href="load.php?id=<?php echo TheMatrix::FILE; ?>&table=<?php echo self::TABLE_USERS; ?>&fields" target="_blank"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&refresh"><?php echo i18n_r(self::FILE.'/REFRESH_INDEX'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&add"><?php echo i18n_r(self::FILE.'/USER_ADD'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&template=profile"><?php echo i18n_r(self::FILE.'/PROFILE'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>" class="current"><?php echo i18n_r(self::FILE.'/USERS'); ?></a>
  <div class="clear"></div>
</div>

<style>
  .page_navigation a:link, 
  .page_navigation a:visited {	 
    font-weight: 100;
    color: #D94136 !important;
    text-decoration: underline;
    padding: 1px 3px;
    background: none !important;
    line-height: 16px;
    -webkit-transition: all .05s ease-in-out;
    -moz-transition: all .05s ease-in-out;
    -o-transition: all .05s ease-in-out;
    transition: all .05s ease-in-out;
  }

  .page_navigation a:hover {
    font-weight: 100;
    background: #D94136 !important;
    color: #fff !important;
    text-decoration: none !important;
    padding: 1px 3px;
    line-height: 16px;
  }

  .page_navigation a em {
    font-style: normal;
  }
  
  .page_navigation a {
  	border-radius:3px;
  }
</style>

<form style="text-align: right;">
  <b><?php echo i18n_r(self::FILE.'/FILTER'); ?> : </b>
  <input class=" autowidth clearfix" style="display: inline; width: 125px;" type="text" id="search_input" placeholder=""/>
</form>

<table class="edittable highlight pajinate">
  <thead>
    <tr>
      <th class="sort" data-sort="displayname"><?php echo i18n_r(self::FILE.'/DISPLAYNAME'); ?></th>
      <th class="sort" data-sort="username"><?php echo i18n_r(self::FILE.'/USERNAME'); ?></th>
      <th class="sort" data-sort="email"><?php echo i18n_r(self::FILE.'/EMAIL'); ?></th>
      <th class="sort" data-sort="level"><?php echo i18n_r(self::FILE.'/LEVEL'); ?></th>
      <th class="sort" data-sort="registered"><?php echo i18n_r(self::FILE.'/REGISTERED'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody class="content">
    <?php foreach ($users as $user) { ?>
      <tr data-displayname="<?php echo $user['displayname']; ?>" data-username="<?php echo $user['username']; ?>" data-email="<?php echo $user['email']; ?>" data-level="<?php echo $user['level']; ?>" data-registered="<?php echo $user['registered']; ?>">  
        <td><a href="load.php?id=<?php echo self::FILE; ?>&user=<?php echo $user['id']; ?>"><?php echo $user['displayname']; ?></a></td>
        <td><?php echo $user['username']; ?></td>
        <td><a href="malto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
        <td><?php echo $this->levels[$user['level']]; ?></td>
        <td><?php echo $user['registered']; ?></td>
        <td style="text-align: right;"><a class="cancel" href="<?php echo $this->getProfileURL($user['username']); ?>" target="_blank">#</a></td>
      </tr>
    <?php } ?>
  </tbody>
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div class="page_navigation" style="overflow: hidden; float: left;"></div>
        <select class="maxUsers" style="float: right;">
          <option value="1">--</option>
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
        </select>
      </th>
    </tr>
  </thead>
</table>



<script>
  $(document).ready(function() {
    // pajination settings
    var pajinateSettings = {
      'items_per_page'  : 10,
      'nav_label_first' : '|&lt;&lt;', 
      'nav_label_prev'  : '&lt;', 
      'nav_label_next'  : '&gt;', 
      'nav_label_last'  : '&gt;&gt;|', 
    };
    
    // pajination
    $('table').pajinate(pajinateSettings);
    
    // filter
    $('#search_input').fastLiveFilter('.content');
    
    // change max number of records
    $('.maxUsers').change(function(){
      pajinateSettings['items_per_page'] = $(this).val();
      $('table').pajinate(pajinateSettings);
    }); // change
    
    // table sorting
    $('table thead .sort').toggle(
      function() {
        $('table').pajinate({'items_per_page': 9999});
        $('table tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'asc'});
        $('table').pajinate(pajinateSettings);
      },
      function () {
        $('table').pajinate({'items_per_page': 9999});
        $('table tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'desc'});
        $('table').pajinate(pajinateSettings);
      }
    ); // toggle
  }); // ready
</script>
