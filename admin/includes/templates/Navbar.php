<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
<div class="container">
  <a href = "dashboard.php" class="navbar-brand"><?php echo lang('HOME_ADMIN')?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="app-nav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="categories.php"><?php echo lang('CATEGORIES')?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="items.php"><?php echo lang('ITEMS')?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="members.php"><?php echo lang('MEMBERS')?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link"
        href="comments.php">
        <?php echo lang('COMMENTS')?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><?php echo lang('STATS')?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#"><?php echo lang('LOGS')?></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Abdallah
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="../index.php">Visit shop</a>
          <a class="dropdown-item" href="members.php?do=edit&userid=<?php echo $_SESSION['ID']?>"><?php echo lang('ADMIN_EDIT')?></a>
          <a class="dropdown-item" href="#"><?php echo lang('ADMIN_SETTINGS')?></a>
          <a class="dropdown-item" href="Logout.php"><?php echo lang('ADMIN_OUT')?></a>
        </div>
      </li>

    </ul>
  </div>
</div>
</nav>