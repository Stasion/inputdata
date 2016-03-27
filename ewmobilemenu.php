<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mmi_pelaku_kreatif", $Language->MenuPhrase("1", "MenuText"), "pelaku_kreatiflist.php", -1, "", AllowListMenu('{D322CAA1-B5DF-4315-A18C-262A6180EE7B}pelaku_kreatif'), FALSE);
$RootMenu->AddMenuItem(2, "mmi_produk", $Language->MenuPhrase("2", "MenuText"), "produklist.php", -1, "", AllowListMenu('{D322CAA1-B5DF-4315-A18C-262A6180EE7B}produk'), FALSE);
$RootMenu->AddMenuItem(3, "mmi_sektor", $Language->MenuPhrase("3", "MenuText"), "sektorlist.php", -1, "", AllowListMenu('{D322CAA1-B5DF-4315-A18C-262A6180EE7B}sektor'), FALSE);
$RootMenu->AddMenuItem(4, "mmi_profile", $Language->MenuPhrase("4", "MenuText"), "profilelist.php", -1, "", AllowListMenu('{D322CAA1-B5DF-4315-A18C-262A6180EE7B}profile'), FALSE);
$RootMenu->AddMenuItem(5, "mmi_produk_kreatif", $Language->MenuPhrase("5", "MenuText"), "produk_kreatiflist.php", -1, "", AllowListMenu('{D322CAA1-B5DF-4315-A18C-262A6180EE7B}produk_kreatif'), FALSE);
$RootMenu->AddMenuItem(-2, "mmi_changepwd", $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
