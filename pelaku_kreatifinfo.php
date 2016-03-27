<?php

// Global variable for table object
$pelaku_kreatif = NULL;

//
// Table class for pelaku_kreatif
//
class cpelaku_kreatif extends cTable {
	var $id;
	var $nama_pemilik;
	var $nama_industri_kreatif;
	var $_email;
	var $password;
	var $deskripsi_industri_kreatif;
	var $alamat;
	var $no_telp;
	var $image;
	var $website;
	var $sektor_industri_kreatif;
	var $order_rank;
	var $created_at;
	var $role;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'pelaku_kreatif';
		$this->TableName = 'pelaku_kreatif';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// id
		$this->id = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_id', 'id', '`id`', '`id`', 3, -1, FALSE, '`id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id'] = &$this->id;

		// nama_pemilik
		$this->nama_pemilik = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_nama_pemilik', 'nama_pemilik', '`nama_pemilik`', '`nama_pemilik`', 200, -1, FALSE, '`nama_pemilik`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_pemilik'] = &$this->nama_pemilik;

		// nama_industri_kreatif
		$this->nama_industri_kreatif = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_nama_industri_kreatif', 'nama_industri_kreatif', '`nama_industri_kreatif`', '`nama_industri_kreatif`', 200, -1, FALSE, '`nama_industri_kreatif`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_industri_kreatif'] = &$this->nama_industri_kreatif;

		// email
		$this->_email = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x__email', 'email', '`email`', '`email`', 200, -1, FALSE, '`email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['email'] = &$this->_email;

		// password
		$this->password = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_password', 'password', '`password`', '`password`', 200, -1, FALSE, '`password`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['password'] = &$this->password;

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_deskripsi_industri_kreatif', 'deskripsi_industri_kreatif', '`deskripsi_industri_kreatif`', '`deskripsi_industri_kreatif`', 201, -1, FALSE, '`deskripsi_industri_kreatif`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['deskripsi_industri_kreatif'] = &$this->deskripsi_industri_kreatif;

		// alamat
		$this->alamat = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_alamat', 'alamat', '`alamat`', '`alamat`', 201, -1, FALSE, '`alamat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['alamat'] = &$this->alamat;

		// no_telp
		$this->no_telp = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_no_telp', 'no_telp', '`no_telp`', '`no_telp`', 200, -1, FALSE, '`no_telp`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['no_telp'] = &$this->no_telp;

		// image
		$this->image = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_image', 'image', '`image`', '`image`', 200, -1, TRUE, '`image`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['image'] = &$this->image;

		// website
		$this->website = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_website', 'website', '`website`', '`website`', 200, -1, FALSE, '`website`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['website'] = &$this->website;

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_sektor_industri_kreatif', 'sektor_industri_kreatif', '`sektor_industri_kreatif`', '`sektor_industri_kreatif`', 3, -1, FALSE, '`sektor_industri_kreatif`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->sektor_industri_kreatif->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['sektor_industri_kreatif'] = &$this->sektor_industri_kreatif;

		// order_rank
		$this->order_rank = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_order_rank', 'order_rank', '`order_rank`', '`order_rank`', 3, -1, FALSE, '`order_rank`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->order_rank->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['order_rank'] = &$this->order_rank;

		// created_at
		$this->created_at = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_created_at', 'created_at', '`created_at`', 'DATE_FORMAT(`created_at`, \'%d/%m/%Y\')', 135, 7, FALSE, '`created_at`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->created_at->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['created_at'] = &$this->created_at;

		// role
		$this->role = new cField('pelaku_kreatif', 'pelaku_kreatif', 'x_role', 'role', '`role`', '`role`', 3, -1, FALSE, '`role`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->role->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['role'] = &$this->role;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`pelaku_kreatif`";
	}

	function SqlFrom() { // For backward compatibility
    	return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
    	$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
    	return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
    	$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
    	return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
    	$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
    	return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
    	$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
    	return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
    	$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
    	return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
    	$this->_SqlOrderBy = $v;
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		global $Security;

		// Add User ID filter
		if (!$this->AllowAnonymousUser() && $Security->CurrentUserID() <> "" && !$Security->IsAdmin()) { // Non system admin
			$sFilter = $this->AddUserIDFilter($sFilter);
		}
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = $this->UserIDAllowSecurity;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`pelaku_kreatif`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			if (EW_ENCRYPTED_PASSWORD && $name == 'password')
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			if (EW_ENCRYPTED_PASSWORD && $name == 'password') {
				$value = (EW_CASE_SENSITIVE_PASSWORD) ? ew_EncryptPassword($value) : ew_EncryptPassword(strtolower($value));
			}
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('id', $rs))
				ew_AddFilter($where, ew_QuotedName('id') . '=' . ew_QuotedValue($rs['id'], $this->id->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`id` = @id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@id@", ew_AdjustSql($this->id->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "pelaku_kreatiflist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "pelaku_kreatiflist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("pelaku_kreatifview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("pelaku_kreatifview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "pelaku_kreatifadd.php?" . $this->UrlParm($parm);
		else
			return "pelaku_kreatifadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("pelaku_kreatifedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("pelaku_kreatifadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("pelaku_kreatifdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->id->CurrentValue)) {
			$sUrl .= "id=" . urlencode($this->id->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["id"]; // id

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->id->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->id->setDbValue($rs->fields('id'));
		$this->nama_pemilik->setDbValue($rs->fields('nama_pemilik'));
		$this->nama_industri_kreatif->setDbValue($rs->fields('nama_industri_kreatif'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->password->setDbValue($rs->fields('password'));
		$this->deskripsi_industri_kreatif->setDbValue($rs->fields('deskripsi_industri_kreatif'));
		$this->alamat->setDbValue($rs->fields('alamat'));
		$this->no_telp->setDbValue($rs->fields('no_telp'));
		$this->image->Upload->DbValue = $rs->fields('image');
		$this->website->setDbValue($rs->fields('website'));
		$this->sektor_industri_kreatif->setDbValue($rs->fields('sektor_industri_kreatif'));
		$this->order_rank->setDbValue($rs->fields('order_rank'));
		$this->created_at->setDbValue($rs->fields('created_at'));
		$this->role->setDbValue($rs->fields('role'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// id
		// nama_pemilik
		// nama_industri_kreatif
		// email
		// password
		// deskripsi_industri_kreatif
		// alamat
		// no_telp
		// image
		// website
		// sektor_industri_kreatif
		// order_rank
		// created_at
		// role
		// id

		$this->id->ViewValue = $this->id->CurrentValue;
		$this->id->ViewCustomAttributes = "";

		// nama_pemilik
		$this->nama_pemilik->ViewValue = $this->nama_pemilik->CurrentValue;
		$this->nama_pemilik->ViewCustomAttributes = "";

		// nama_industri_kreatif
		$this->nama_industri_kreatif->ViewValue = $this->nama_industri_kreatif->CurrentValue;
		$this->nama_industri_kreatif->ViewCustomAttributes = "";

		// email
		$this->_email->ViewValue = $this->_email->CurrentValue;
		$this->_email->ViewCustomAttributes = "";

		// password
		$this->password->ViewValue = "********";
		$this->password->ViewCustomAttributes = "";

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->ViewValue = $this->deskripsi_industri_kreatif->CurrentValue;
		$this->deskripsi_industri_kreatif->ViewCustomAttributes = "";

		// alamat
		$this->alamat->ViewValue = $this->alamat->CurrentValue;
		$this->alamat->ViewCustomAttributes = "";

		// no_telp
		$this->no_telp->ViewValue = $this->no_telp->CurrentValue;
		$this->no_telp->ViewCustomAttributes = "";

		// image
		if (!ew_Empty($this->image->Upload->DbValue)) {
			$this->image->ImageAlt = $this->image->FldAlt();
			$this->image->ViewValue = ew_UploadPathEx(FALSE, $this->image->UploadPath) . $this->image->Upload->DbValue;
			if ($this->CustomExport == "pdf" || $this->CustomExport == "email") {
				$this->image->ViewValue = ew_UploadPathEx(TRUE, $this->image->UploadPath) . $this->image->Upload->DbValue;
			}
		} else {
			$this->image->ViewValue = "";
		}
		$this->image->ViewCustomAttributes = "";

		// website
		$this->website->ViewValue = $this->website->CurrentValue;
		$this->website->ViewCustomAttributes = "";

		// sektor_industri_kreatif
		if (strval($this->sektor_industri_kreatif->CurrentValue) <> "") {
			$sFilterWrk = "`id`" . ew_SearchString("=", $this->sektor_industri_kreatif->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `id`, `nama_sektor` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `sektor`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->sektor_industri_kreatif, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->sektor_industri_kreatif->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->sektor_industri_kreatif->ViewValue = $this->sektor_industri_kreatif->CurrentValue;
			}
		} else {
			$this->sektor_industri_kreatif->ViewValue = NULL;
		}
		$this->sektor_industri_kreatif->ViewCustomAttributes = "";

		// order_rank
		if (strval($this->order_rank->CurrentValue) <> "") {
			switch ($this->order_rank->CurrentValue) {
				case $this->order_rank->FldTagValue(1):
					$this->order_rank->ViewValue = $this->order_rank->FldTagCaption(1) <> "" ? $this->order_rank->FldTagCaption(1) : $this->order_rank->CurrentValue;
					break;
				case $this->order_rank->FldTagValue(2):
					$this->order_rank->ViewValue = $this->order_rank->FldTagCaption(2) <> "" ? $this->order_rank->FldTagCaption(2) : $this->order_rank->CurrentValue;
					break;
				case $this->order_rank->FldTagValue(3):
					$this->order_rank->ViewValue = $this->order_rank->FldTagCaption(3) <> "" ? $this->order_rank->FldTagCaption(3) : $this->order_rank->CurrentValue;
					break;
				case $this->order_rank->FldTagValue(4):
					$this->order_rank->ViewValue = $this->order_rank->FldTagCaption(4) <> "" ? $this->order_rank->FldTagCaption(4) : $this->order_rank->CurrentValue;
					break;
				case $this->order_rank->FldTagValue(5):
					$this->order_rank->ViewValue = $this->order_rank->FldTagCaption(5) <> "" ? $this->order_rank->FldTagCaption(5) : $this->order_rank->CurrentValue;
					break;
				default:
					$this->order_rank->ViewValue = $this->order_rank->CurrentValue;
			}
		} else {
			$this->order_rank->ViewValue = NULL;
		}
		$this->order_rank->ViewCustomAttributes = "";

		// created_at
		$this->created_at->ViewValue = $this->created_at->CurrentValue;
		$this->created_at->ViewValue = ew_FormatDateTime($this->created_at->ViewValue, 7);
		$this->created_at->ViewCustomAttributes = "";

		// role
		if ($Security->CanAdmin()) { // System admin
		if (strval($this->role->CurrentValue) <> "") {
			switch ($this->role->CurrentValue) {
				case $this->role->FldTagValue(1):
					$this->role->ViewValue = $this->role->FldTagCaption(1) <> "" ? $this->role->FldTagCaption(1) : $this->role->CurrentValue;
					break;
				case $this->role->FldTagValue(2):
					$this->role->ViewValue = $this->role->FldTagCaption(2) <> "" ? $this->role->FldTagCaption(2) : $this->role->CurrentValue;
					break;
				case $this->role->FldTagValue(3):
					$this->role->ViewValue = $this->role->FldTagCaption(3) <> "" ? $this->role->FldTagCaption(3) : $this->role->CurrentValue;
					break;
				default:
					$this->role->ViewValue = $this->role->CurrentValue;
			}
		} else {
			$this->role->ViewValue = NULL;
		}
		} else {
			$this->role->ViewValue = "********";
		}
		$this->role->ViewCustomAttributes = "";

		// id
		$this->id->LinkCustomAttributes = "";
		$this->id->HrefValue = "";
		$this->id->TooltipValue = "";

		// nama_pemilik
		$this->nama_pemilik->LinkCustomAttributes = "";
		$this->nama_pemilik->HrefValue = "";
		$this->nama_pemilik->TooltipValue = "";

		// nama_industri_kreatif
		$this->nama_industri_kreatif->LinkCustomAttributes = "";
		$this->nama_industri_kreatif->HrefValue = "";
		$this->nama_industri_kreatif->TooltipValue = "";

		// email
		$this->_email->LinkCustomAttributes = "";
		$this->_email->HrefValue = "";
		$this->_email->TooltipValue = "";

		// password
		$this->password->LinkCustomAttributes = "";
		$this->password->HrefValue = "";
		$this->password->TooltipValue = "";

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->LinkCustomAttributes = "";
		$this->deskripsi_industri_kreatif->HrefValue = "";
		$this->deskripsi_industri_kreatif->TooltipValue = "";

		// alamat
		$this->alamat->LinkCustomAttributes = "";
		$this->alamat->HrefValue = "";
		$this->alamat->TooltipValue = "";

		// no_telp
		$this->no_telp->LinkCustomAttributes = "";
		$this->no_telp->HrefValue = "";
		$this->no_telp->TooltipValue = "";

		// image
		$this->image->LinkCustomAttributes = "";
		if (!ew_Empty($this->image->Upload->DbValue)) {
			$this->image->HrefValue = ew_UploadPathEx(FALSE, $this->image->UploadPath) . $this->image->Upload->DbValue; // Add prefix/suffix
			$this->image->LinkAttrs["target"] = ""; // Add target
			if ($this->Export <> "") $this->image->HrefValue = ew_ConvertFullUrl($this->image->HrefValue);
		} else {
			$this->image->HrefValue = "";
		}
		$this->image->HrefValue2 = $this->image->UploadPath . $this->image->Upload->DbValue;
		$this->image->TooltipValue = "";
		if ($this->image->UseColorbox) {
			$this->image->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
			$this->image->LinkAttrs["data-rel"] = "pelaku_kreatif_x_image";
			$this->image->LinkAttrs["class"] = "ewLightbox";
		}

		// website
		$this->website->LinkCustomAttributes = "";
		$this->website->HrefValue = "";
		$this->website->TooltipValue = "";

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif->LinkCustomAttributes = "";
		$this->sektor_industri_kreatif->HrefValue = "";
		$this->sektor_industri_kreatif->TooltipValue = "";

		// order_rank
		$this->order_rank->LinkCustomAttributes = "";
		$this->order_rank->HrefValue = "";
		$this->order_rank->TooltipValue = "";

		// created_at
		$this->created_at->LinkCustomAttributes = "";
		$this->created_at->HrefValue = "";
		$this->created_at->TooltipValue = "";

		// role
		$this->role->LinkCustomAttributes = "";
		$this->role->HrefValue = "";
		$this->role->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// id
		$this->id->EditAttrs["class"] = "form-control";
		$this->id->EditCustomAttributes = "";
		$this->id->EditValue = $this->id->CurrentValue;
		$this->id->ViewCustomAttributes = "";

		// nama_pemilik
		$this->nama_pemilik->EditAttrs["class"] = "form-control";
		$this->nama_pemilik->EditCustomAttributes = "";
		$this->nama_pemilik->EditValue = ew_HtmlEncode($this->nama_pemilik->CurrentValue);
		$this->nama_pemilik->PlaceHolder = ew_RemoveHtml($this->nama_pemilik->FldCaption());

		// nama_industri_kreatif
		$this->nama_industri_kreatif->EditAttrs["class"] = "form-control";
		$this->nama_industri_kreatif->EditCustomAttributes = "";
		$this->nama_industri_kreatif->EditValue = ew_HtmlEncode($this->nama_industri_kreatif->CurrentValue);
		$this->nama_industri_kreatif->PlaceHolder = ew_RemoveHtml($this->nama_industri_kreatif->FldCaption());

		// email
		$this->_email->EditAttrs["class"] = "form-control";
		$this->_email->EditCustomAttributes = "";
		$this->_email->EditValue = ew_HtmlEncode($this->_email->CurrentValue);
		$this->_email->PlaceHolder = ew_RemoveHtml($this->_email->FldCaption());

		// password
		$this->password->EditAttrs["class"] = "form-control";
		$this->password->EditCustomAttributes = "";
		$this->password->EditValue = ew_HtmlEncode($this->password->CurrentValue);
		$this->password->PlaceHolder = ew_RemoveHtml($this->password->FldCaption());

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->EditAttrs["class"] = "form-control";
		$this->deskripsi_industri_kreatif->EditCustomAttributes = "";
		$this->deskripsi_industri_kreatif->EditValue = ew_HtmlEncode($this->deskripsi_industri_kreatif->CurrentValue);
		$this->deskripsi_industri_kreatif->PlaceHolder = ew_RemoveHtml($this->deskripsi_industri_kreatif->FldCaption());

		// alamat
		$this->alamat->EditAttrs["class"] = "form-control";
		$this->alamat->EditCustomAttributes = "";
		$this->alamat->EditValue = ew_HtmlEncode($this->alamat->CurrentValue);
		$this->alamat->PlaceHolder = ew_RemoveHtml($this->alamat->FldCaption());

		// no_telp
		$this->no_telp->EditAttrs["class"] = "form-control";
		$this->no_telp->EditCustomAttributes = "";
		$this->no_telp->EditValue = ew_HtmlEncode($this->no_telp->CurrentValue);
		$this->no_telp->PlaceHolder = ew_RemoveHtml($this->no_telp->FldCaption());

		// image
		$this->image->EditAttrs["class"] = "form-control";
		$this->image->EditCustomAttributes = "";
		if (!ew_Empty($this->image->Upload->DbValue)) {
			$this->image->ImageAlt = $this->image->FldAlt();
			$this->image->EditValue = ew_UploadPathEx(FALSE, $this->image->UploadPath) . $this->image->Upload->DbValue;
			if ($this->CustomExport == "pdf" || $this->CustomExport == "email") {
				$this->image->EditValue = ew_UploadPathEx(TRUE, $this->image->UploadPath) . $this->image->Upload->DbValue;
			}
		} else {
			$this->image->EditValue = "";
		}
		if (!ew_Empty($this->image->CurrentValue))
			$this->image->Upload->FileName = $this->image->CurrentValue;

		// website
		$this->website->EditAttrs["class"] = "form-control";
		$this->website->EditCustomAttributes = "";
		$this->website->EditValue = ew_HtmlEncode($this->website->CurrentValue);
		$this->website->PlaceHolder = ew_RemoveHtml($this->website->FldCaption());

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif->EditAttrs["class"] = "form-control";
		$this->sektor_industri_kreatif->EditCustomAttributes = "";

		// order_rank
		$this->order_rank->EditAttrs["class"] = "form-control";
		$this->order_rank->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->order_rank->FldTagValue(1), $this->order_rank->FldTagCaption(1) <> "" ? $this->order_rank->FldTagCaption(1) : $this->order_rank->FldTagValue(1));
		$arwrk[] = array($this->order_rank->FldTagValue(2), $this->order_rank->FldTagCaption(2) <> "" ? $this->order_rank->FldTagCaption(2) : $this->order_rank->FldTagValue(2));
		$arwrk[] = array($this->order_rank->FldTagValue(3), $this->order_rank->FldTagCaption(3) <> "" ? $this->order_rank->FldTagCaption(3) : $this->order_rank->FldTagValue(3));
		$arwrk[] = array($this->order_rank->FldTagValue(4), $this->order_rank->FldTagCaption(4) <> "" ? $this->order_rank->FldTagCaption(4) : $this->order_rank->FldTagValue(4));
		$arwrk[] = array($this->order_rank->FldTagValue(5), $this->order_rank->FldTagCaption(5) <> "" ? $this->order_rank->FldTagCaption(5) : $this->order_rank->FldTagValue(5));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->order_rank->EditValue = $arwrk;

		// created_at
		$this->created_at->EditAttrs["class"] = "form-control";
		$this->created_at->EditCustomAttributes = "";
		$this->created_at->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->created_at->CurrentValue, 7));
		$this->created_at->PlaceHolder = ew_RemoveHtml($this->created_at->FldCaption());

		// role
		$this->role->EditAttrs["class"] = "form-control";
		$this->role->EditCustomAttributes = "";
		if (!$Security->CanAdmin()) { // System admin
			$this->role->EditValue = "********";
		} else {
		$arwrk = array();
		$arwrk[] = array($this->role->FldTagValue(1), $this->role->FldTagCaption(1) <> "" ? $this->role->FldTagCaption(1) : $this->role->FldTagValue(1));
		$arwrk[] = array($this->role->FldTagValue(2), $this->role->FldTagCaption(2) <> "" ? $this->role->FldTagCaption(2) : $this->role->FldTagValue(2));
		$arwrk[] = array($this->role->FldTagValue(3), $this->role->FldTagCaption(3) <> "" ? $this->role->FldTagCaption(3) : $this->role->FldTagValue(3));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->role->EditValue = $arwrk;
		}

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->id->Exportable) $Doc->ExportCaption($this->id);
					if ($this->nama_pemilik->Exportable) $Doc->ExportCaption($this->nama_pemilik);
					if ($this->nama_industri_kreatif->Exportable) $Doc->ExportCaption($this->nama_industri_kreatif);
					if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
					if ($this->password->Exportable) $Doc->ExportCaption($this->password);
					if ($this->deskripsi_industri_kreatif->Exportable) $Doc->ExportCaption($this->deskripsi_industri_kreatif);
					if ($this->alamat->Exportable) $Doc->ExportCaption($this->alamat);
					if ($this->no_telp->Exportable) $Doc->ExportCaption($this->no_telp);
					if ($this->image->Exportable) $Doc->ExportCaption($this->image);
					if ($this->website->Exportable) $Doc->ExportCaption($this->website);
					if ($this->sektor_industri_kreatif->Exportable) $Doc->ExportCaption($this->sektor_industri_kreatif);
					if ($this->order_rank->Exportable) $Doc->ExportCaption($this->order_rank);
					if ($this->created_at->Exportable) $Doc->ExportCaption($this->created_at);
					if ($this->role->Exportable) $Doc->ExportCaption($this->role);
				} else {
					if ($this->id->Exportable) $Doc->ExportCaption($this->id);
					if ($this->nama_pemilik->Exportable) $Doc->ExportCaption($this->nama_pemilik);
					if ($this->nama_industri_kreatif->Exportable) $Doc->ExportCaption($this->nama_industri_kreatif);
					if ($this->_email->Exportable) $Doc->ExportCaption($this->_email);
					if ($this->password->Exportable) $Doc->ExportCaption($this->password);
					if ($this->no_telp->Exportable) $Doc->ExportCaption($this->no_telp);
					if ($this->website->Exportable) $Doc->ExportCaption($this->website);
					if ($this->sektor_industri_kreatif->Exportable) $Doc->ExportCaption($this->sektor_industri_kreatif);
					if ($this->order_rank->Exportable) $Doc->ExportCaption($this->order_rank);
					if ($this->created_at->Exportable) $Doc->ExportCaption($this->created_at);
					if ($this->role->Exportable) $Doc->ExportCaption($this->role);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->id->Exportable) $Doc->ExportField($this->id);
						if ($this->nama_pemilik->Exportable) $Doc->ExportField($this->nama_pemilik);
						if ($this->nama_industri_kreatif->Exportable) $Doc->ExportField($this->nama_industri_kreatif);
						if ($this->_email->Exportable) $Doc->ExportField($this->_email);
						if ($this->password->Exportable) $Doc->ExportField($this->password);
						if ($this->deskripsi_industri_kreatif->Exportable) $Doc->ExportField($this->deskripsi_industri_kreatif);
						if ($this->alamat->Exportable) $Doc->ExportField($this->alamat);
						if ($this->no_telp->Exportable) $Doc->ExportField($this->no_telp);
						if ($this->image->Exportable) $Doc->ExportField($this->image);
						if ($this->website->Exportable) $Doc->ExportField($this->website);
						if ($this->sektor_industri_kreatif->Exportable) $Doc->ExportField($this->sektor_industri_kreatif);
						if ($this->order_rank->Exportable) $Doc->ExportField($this->order_rank);
						if ($this->created_at->Exportable) $Doc->ExportField($this->created_at);
						if ($this->role->Exportable) $Doc->ExportField($this->role);
					} else {
						if ($this->id->Exportable) $Doc->ExportField($this->id);
						if ($this->nama_pemilik->Exportable) $Doc->ExportField($this->nama_pemilik);
						if ($this->nama_industri_kreatif->Exportable) $Doc->ExportField($this->nama_industri_kreatif);
						if ($this->_email->Exportable) $Doc->ExportField($this->_email);
						if ($this->password->Exportable) $Doc->ExportField($this->password);
						if ($this->no_telp->Exportable) $Doc->ExportField($this->no_telp);
						if ($this->website->Exportable) $Doc->ExportField($this->website);
						if ($this->sektor_industri_kreatif->Exportable) $Doc->ExportField($this->sektor_industri_kreatif);
						if ($this->order_rank->Exportable) $Doc->ExportField($this->order_rank);
						if ($this->created_at->Exportable) $Doc->ExportField($this->created_at);
						if ($this->role->Exportable) $Doc->ExportField($this->role);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// User ID filter
	function UserIDFilter($userid) {
		$sUserIDFilter = '`id` = ' . ew_QuotedValue($userid, EW_DATATYPE_NUMBER);
		return $sUserIDFilter;
	}

	// Add User ID filter
	function AddUserIDFilter($sFilter) {
		global $Security;
		$sFilterWrk = "";
		$id = (CurrentPageID() == "list") ? $this->CurrentAction : CurrentPageID();
		if (!$this->UserIDAllow($id) && !$Security->IsAdmin()) {
			$sFilterWrk = $Security->UserIDList();
			if ($sFilterWrk <> "")
				$sFilterWrk = '`id` IN (' . $sFilterWrk . ')';
		}

		// Call User ID Filtering event
		$this->UserID_Filtering($sFilterWrk);
		ew_AddFilter($sFilter, $sFilterWrk);
		return $sFilter;
	}

	// User ID subquery
	function GetUserIDSubquery(&$fld, &$masterfld) {
		global $conn;
		$sWrk = "";
		$sSql = "SELECT " . $masterfld->FldExpression . " FROM `pelaku_kreatif`";
		$sFilter = $this->AddUserIDFilter("");
		if ($sFilter <> "") $sSql .= " WHERE " . $sFilter;

		// Use subquery
		if (EW_USE_SUBQUERY_FOR_MASTER_USER_ID) {
			$sWrk = $sSql;
		} else {

			// List all values
			if ($rs = $conn->Execute($sSql)) {
				while (!$rs->EOF) {
					if ($sWrk <> "") $sWrk .= ",";
					$sWrk .= ew_QuotedValue($rs->fields[0], $masterfld->FldDataType);
					$rs->MoveNext();
				}
				$rs->Close();
			}
		}
		if ($sWrk <> "") {
			$sWrk = $fld->FldExpression . " IN (" . $sWrk . ")";
		}
		return $sWrk;
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
		resizeImage("uploads/".$rsnew['image']);
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
