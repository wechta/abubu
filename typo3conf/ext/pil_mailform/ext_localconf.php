<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_pilmailform_pi1.php","_pi1","list_type", 0);
t3lib_extMgm::addTypoScript($_EXTKEY,"setup","plugin.".t3lib_extMgm::getCN($_EXTKEY)."_pi1 = USER_INT",43);
?>
