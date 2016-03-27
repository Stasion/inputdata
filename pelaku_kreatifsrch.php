<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "pelaku_kreatifinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$pelaku_kreatif_search = NULL; // Initialize page object first

class cpelaku_kreatif_search extends cpelaku_kreatif {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'pelaku_kreatif';

	// Page object name
	var $PageObjName = 'pelaku_kreatif_search';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (pelaku_kreatif)
		if (!isset($GLOBALS["pelaku_kreatif"]) || get_class($GLOBALS["pelaku_kreatif"]) == "cpelaku_kreatif") {
			$GLOBALS["pelaku_kreatif"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["pelaku_kreatif"];
		}

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pelaku_kreatif', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("pelaku_kreatiflist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("pelaku_kreatiflist.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $pelaku_kreatif;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($pelaku_kreatif);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "pelaku_kreatiflist.php" . "?" . $sSrchStr;
						if ($this->IsModal) {
							$row = array();
							$row["url"] = $sSrchStr;
							echo ew_ArrayToJson(array($row));
							$this->Page_Terminate();
							exit();
						} else {
							$this->Page_Terminate($sSrchStr); // Go to list page
						}
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->id); // id
		$this->BuildSearchUrl($sSrchUrl, $this->nama_pemilik); // nama_pemilik
		$this->BuildSearchUrl($sSrchUrl, $this->nama_industri_kreatif); // nama_industri_kreatif
		$this->BuildSearchUrl($sSrchUrl, $this->_email); // email
		$this->BuildSearchUrl($sSrchUrl, $this->password); // password
		$this->BuildSearchUrl($sSrchUrl, $this->deskripsi_industri_kreatif); // deskripsi_industri_kreatif
		$this->BuildSearchUrl($sSrchUrl, $this->alamat); // alamat
		$this->BuildSearchUrl($sSrchUrl, $this->no_telp); // no_telp
		$this->BuildSearchUrl($sSrchUrl, $this->image); // image
		$this->BuildSearchUrl($sSrchUrl, $this->website); // website
		$this->BuildSearchUrl($sSrchUrl, $this->sektor_industri_kreatif); // sektor_industri_kreatif
		$this->BuildSearchUrl($sSrchUrl, $this->order_rank); // order_rank
		$this->BuildSearchUrl($sSrchUrl, $this->created_at); // created_at
		$this->BuildSearchUrl($sSrchUrl, $this->role); // role
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// id

		$this->id->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id"));
		$this->id->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id");

		// nama_pemilik
		$this->nama_pemilik->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nama_pemilik"));
		$this->nama_pemilik->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nama_pemilik");

		// nama_industri_kreatif
		$this->nama_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nama_industri_kreatif"));
		$this->nama_industri_kreatif->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nama_industri_kreatif");

		// email
		$this->_email->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__email"));
		$this->_email->AdvancedSearch->SearchOperator = $objForm->GetValue("z__email");

		// password
		$this->password->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_password"));
		$this->password->AdvancedSearch->SearchOperator = $objForm->GetValue("z_password");

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_deskripsi_industri_kreatif"));
		$this->deskripsi_industri_kreatif->AdvancedSearch->SearchOperator = $objForm->GetValue("z_deskripsi_industri_kreatif");

		// alamat
		$this->alamat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_alamat"));
		$this->alamat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_alamat");

		// no_telp
		$this->no_telp->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_no_telp"));
		$this->no_telp->AdvancedSearch->SearchOperator = $objForm->GetValue("z_no_telp");

		// image
		$this->image->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_image"));
		$this->image->AdvancedSearch->SearchOperator = $objForm->GetValue("z_image");

		// website
		$this->website->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_website"));
		$this->website->AdvancedSearch->SearchOperator = $objForm->GetValue("z_website");

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_sektor_industri_kreatif"));
		$this->sektor_industri_kreatif->AdvancedSearch->SearchOperator = $objForm->GetValue("z_sektor_industri_kreatif");

		// order_rank
		$this->order_rank->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_order_rank"));
		$this->order_rank->AdvancedSearch->SearchOperator = $objForm->GetValue("z_order_rank");

		// created_at
		$this->created_at->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_created_at"));
		$this->created_at->AdvancedSearch->SearchOperator = $objForm->GetValue("z_created_at");

		// role
		$this->role->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_role"));
		$this->role->AdvancedSearch->SearchOperator = $objForm->GetValue("z_role");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id
			$this->id->EditAttrs["class"] = "form-control";
			$this->id->EditCustomAttributes = "";
			if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow("search")) { // Non system admin
				$this->id->AdvancedSearch->SearchValue = CurrentUserID();
			$this->id->EditValue = $this->id->AdvancedSearch->SearchValue;
			$this->id->ViewCustomAttributes = "";
			} else {
			$this->id->EditValue = ew_HtmlEncode($this->id->AdvancedSearch->SearchValue);
			$this->id->PlaceHolder = ew_RemoveHtml($this->id->FldCaption());
			}

			// nama_pemilik
			$this->nama_pemilik->EditAttrs["class"] = "form-control";
			$this->nama_pemilik->EditCustomAttributes = "";
			$this->nama_pemilik->EditValue = ew_HtmlEncode($this->nama_pemilik->AdvancedSearch->SearchValue);
			$this->nama_pemilik->PlaceHolder = ew_RemoveHtml($this->nama_pemilik->FldCaption());

			// nama_industri_kreatif
			$this->nama_industri_kreatif->EditAttrs["class"] = "form-control";
			$this->nama_industri_kreatif->EditCustomAttributes = "";
			$this->nama_industri_kreatif->EditValue = ew_HtmlEncode($this->nama_industri_kreatif->AdvancedSearch->SearchValue);
			$this->nama_industri_kreatif->PlaceHolder = ew_RemoveHtml($this->nama_industri_kreatif->FldCaption());

			// email
			$this->_email->EditAttrs["class"] = "form-control";
			$this->_email->EditCustomAttributes = "";
			$this->_email->EditValue = ew_HtmlEncode($this->_email->AdvancedSearch->SearchValue);
			$this->_email->PlaceHolder = ew_RemoveHtml($this->_email->FldCaption());

			// password
			$this->password->EditAttrs["class"] = "form-control";
			$this->password->EditCustomAttributes = "";
			$this->password->EditValue = ew_HtmlEncode($this->password->AdvancedSearch->SearchValue);
			$this->password->PlaceHolder = ew_RemoveHtml($this->password->FldCaption());

			// deskripsi_industri_kreatif
			$this->deskripsi_industri_kreatif->EditAttrs["class"] = "form-control";
			$this->deskripsi_industri_kreatif->EditCustomAttributes = "";
			$this->deskripsi_industri_kreatif->EditValue = ew_HtmlEncode($this->deskripsi_industri_kreatif->AdvancedSearch->SearchValue);
			$this->deskripsi_industri_kreatif->PlaceHolder = ew_RemoveHtml($this->deskripsi_industri_kreatif->FldCaption());

			// alamat
			$this->alamat->EditAttrs["class"] = "form-control";
			$this->alamat->EditCustomAttributes = "";
			$this->alamat->EditValue = ew_HtmlEncode($this->alamat->AdvancedSearch->SearchValue);
			$this->alamat->PlaceHolder = ew_RemoveHtml($this->alamat->FldCaption());

			// no_telp
			$this->no_telp->EditAttrs["class"] = "form-control";
			$this->no_telp->EditCustomAttributes = "";
			$this->no_telp->EditValue = ew_HtmlEncode($this->no_telp->AdvancedSearch->SearchValue);
			$this->no_telp->PlaceHolder = ew_RemoveHtml($this->no_telp->FldCaption());

			// image
			$this->image->EditAttrs["class"] = "form-control";
			$this->image->EditCustomAttributes = "";
			$this->image->EditValue = ew_HtmlEncode($this->image->AdvancedSearch->SearchValue);
			$this->image->PlaceHolder = ew_RemoveHtml($this->image->FldCaption());

			// website
			$this->website->EditAttrs["class"] = "form-control";
			$this->website->EditCustomAttributes = "";
			$this->website->EditValue = ew_HtmlEncode($this->website->AdvancedSearch->SearchValue);
			$this->website->PlaceHolder = ew_RemoveHtml($this->website->FldCaption());

			// sektor_industri_kreatif
			$this->sektor_industri_kreatif->EditAttrs["class"] = "form-control";
			$this->sektor_industri_kreatif->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id`, `nama_sektor` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `sektor`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->sektor_industri_kreatif, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->sektor_industri_kreatif->EditValue = $arwrk;

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
			$this->created_at->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->created_at->AdvancedSearch->SearchValue, 7), 7));
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
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckInteger($this->id->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->id->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->created_at->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->created_at->FldErrMsg());
		}

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->id->AdvancedSearch->Load();
		$this->nama_pemilik->AdvancedSearch->Load();
		$this->nama_industri_kreatif->AdvancedSearch->Load();
		$this->_email->AdvancedSearch->Load();
		$this->password->AdvancedSearch->Load();
		$this->deskripsi_industri_kreatif->AdvancedSearch->Load();
		$this->alamat->AdvancedSearch->Load();
		$this->no_telp->AdvancedSearch->Load();
		$this->image->AdvancedSearch->Load();
		$this->website->AdvancedSearch->Load();
		$this->sektor_industri_kreatif->AdvancedSearch->Load();
		$this->order_rank->AdvancedSearch->Load();
		$this->created_at->AdvancedSearch->Load();
		$this->role->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "pelaku_kreatiflist.php", "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($pelaku_kreatif_search)) $pelaku_kreatif_search = new cpelaku_kreatif_search();

// Page init
$pelaku_kreatif_search->Page_Init();

// Page main
$pelaku_kreatif_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pelaku_kreatif_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pelaku_kreatif_search = new ew_Page("pelaku_kreatif_search");
pelaku_kreatif_search.PageID = "search"; // Page ID
var EW_PAGE_ID = pelaku_kreatif_search.PageID; // For backward compatibility

// Form object
var fpelaku_kreatifsearch = new ew_Form("fpelaku_kreatifsearch");

// Form_CustomValidate event
fpelaku_kreatifsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpelaku_kreatifsearch.ValidateRequired = true;
<?php } else { ?>
fpelaku_kreatifsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpelaku_kreatifsearch.Lists["x_sektor_industri_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_sektor","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

fpelaku_kreatifsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_id");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($pelaku_kreatif->id->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_created_at");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($pelaku_kreatif->created_at->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$pelaku_kreatif_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $pelaku_kreatif_search->ShowPageHeader(); ?>
<?php
$pelaku_kreatif_search->ShowMessage();
?>
<form name="fpelaku_kreatifsearch" id="fpelaku_kreatifsearch" class="form-horizontal ewForm ewSearchForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pelaku_kreatif_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pelaku_kreatif_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pelaku_kreatif">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($pelaku_kreatif_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($pelaku_kreatif->id->Visible) { // id ?>
	<div id="r_id" class="form-group">
		<label for="x_id" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_id"><?php echo $pelaku_kreatif->id->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id" id="z_id" value="="></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->id->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_id">
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$pelaku_kreatif->UserIDAllow("search")) { // Non system admin ?>
<span<?php echo $pelaku_kreatif->id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $pelaku_kreatif->id->EditValue ?></p></span>
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($pelaku_kreatif->id->AdvancedSearch->SearchValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_id" name="x_id" id="x_id" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->id->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->id->EditValue ?>"<?php echo $pelaku_kreatif->id->EditAttributes() ?>>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->nama_pemilik->Visible) { // nama_pemilik ?>
	<div id="r_nama_pemilik" class="form-group">
		<label for="x_nama_pemilik" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_nama_pemilik"><?php echo $pelaku_kreatif->nama_pemilik->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nama_pemilik" id="z_nama_pemilik" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->nama_pemilik->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_nama_pemilik">
<input type="text" data-field="x_nama_pemilik" name="x_nama_pemilik" id="x_nama_pemilik" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->nama_pemilik->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->nama_pemilik->EditValue ?>"<?php echo $pelaku_kreatif->nama_pemilik->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->nama_industri_kreatif->Visible) { // nama_industri_kreatif ?>
	<div id="r_nama_industri_kreatif" class="form-group">
		<label for="x_nama_industri_kreatif" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_nama_industri_kreatif"><?php echo $pelaku_kreatif->nama_industri_kreatif->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nama_industri_kreatif" id="z_nama_industri_kreatif" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->nama_industri_kreatif->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_nama_industri_kreatif">
<input type="text" data-field="x_nama_industri_kreatif" name="x_nama_industri_kreatif" id="x_nama_industri_kreatif" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->nama_industri_kreatif->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->nama_industri_kreatif->EditValue ?>"<?php echo $pelaku_kreatif->nama_industri_kreatif->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->_email->Visible) { // email ?>
	<div id="r__email" class="form-group">
		<label for="x__email" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif__email"><?php echo $pelaku_kreatif->_email->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__email" id="z__email" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->_email->CellAttributes() ?>>
			<span id="el_pelaku_kreatif__email">
<input type="text" data-field="x__email" name="x__email" id="x__email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->_email->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->_email->EditValue ?>"<?php echo $pelaku_kreatif->_email->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->password->Visible) { // password ?>
	<div id="r_password" class="form-group">
		<label for="x_password" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_password"><?php echo $pelaku_kreatif->password->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_password" id="z_password" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->password->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_password">
<input type="password" data-field="x_password" name="x_password" id="x_password" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->password->PlaceHolder) ?>"<?php echo $pelaku_kreatif->password->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->deskripsi_industri_kreatif->Visible) { // deskripsi_industri_kreatif ?>
	<div id="r_deskripsi_industri_kreatif" class="form-group">
		<label for="x_deskripsi_industri_kreatif" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_deskripsi_industri_kreatif"><?php echo $pelaku_kreatif->deskripsi_industri_kreatif->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_deskripsi_industri_kreatif" id="z_deskripsi_industri_kreatif" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_deskripsi_industri_kreatif">
<input type="text" data-field="x_deskripsi_industri_kreatif" name="x_deskripsi_industri_kreatif" id="x_deskripsi_industri_kreatif" size="70" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->deskripsi_industri_kreatif->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->EditValue ?>"<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->alamat->Visible) { // alamat ?>
	<div id="r_alamat" class="form-group">
		<label for="x_alamat" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_alamat"><?php echo $pelaku_kreatif->alamat->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_alamat" id="z_alamat" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->alamat->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_alamat">
<input type="text" data-field="x_alamat" name="x_alamat" id="x_alamat" size="35" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->alamat->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->alamat->EditValue ?>"<?php echo $pelaku_kreatif->alamat->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->no_telp->Visible) { // no_telp ?>
	<div id="r_no_telp" class="form-group">
		<label for="x_no_telp" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_no_telp"><?php echo $pelaku_kreatif->no_telp->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_no_telp" id="z_no_telp" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->no_telp->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_no_telp">
<input type="text" data-field="x_no_telp" name="x_no_telp" id="x_no_telp" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->no_telp->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->no_telp->EditValue ?>"<?php echo $pelaku_kreatif->no_telp->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->image->Visible) { // image ?>
	<div id="r_image" class="form-group">
		<label class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_image"><?php echo $pelaku_kreatif->image->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_image" id="z_image" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->image->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_image">
<input type="text" data-field="x_image" name="x_image" id="x_image" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->image->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->image->EditValue ?>"<?php echo $pelaku_kreatif->image->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->website->Visible) { // website ?>
	<div id="r_website" class="form-group">
		<label for="x_website" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_website"><?php echo $pelaku_kreatif->website->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_website" id="z_website" value="LIKE"></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->website->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_website">
<input type="text" data-field="x_website" name="x_website" id="x_website" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->website->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->website->EditValue ?>"<?php echo $pelaku_kreatif->website->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->sektor_industri_kreatif->Visible) { // sektor_industri_kreatif ?>
	<div id="r_sektor_industri_kreatif" class="form-group">
		<label for="x_sektor_industri_kreatif" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_sektor_industri_kreatif"><?php echo $pelaku_kreatif->sektor_industri_kreatif->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_sektor_industri_kreatif" id="z_sektor_industri_kreatif" value="="></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->sektor_industri_kreatif->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_sektor_industri_kreatif">
<select data-field="x_sektor_industri_kreatif" id="x_sektor_industri_kreatif" name="x_sektor_industri_kreatif"<?php echo $pelaku_kreatif->sektor_industri_kreatif->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->sektor_industri_kreatif->EditValue)) {
	$arwrk = $pelaku_kreatif->sektor_industri_kreatif->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->sektor_industri_kreatif->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fpelaku_kreatifsearch.Lists["x_sektor_industri_kreatif"].Options = <?php echo (is_array($pelaku_kreatif->sektor_industri_kreatif->EditValue)) ? ew_ArrayToJson($pelaku_kreatif->sektor_industri_kreatif->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->order_rank->Visible) { // order_rank ?>
	<div id="r_order_rank" class="form-group">
		<label for="x_order_rank" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_order_rank"><?php echo $pelaku_kreatif->order_rank->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_order_rank" id="z_order_rank" value="="></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->order_rank->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_order_rank">
<select data-field="x_order_rank" id="x_order_rank" name="x_order_rank"<?php echo $pelaku_kreatif->order_rank->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->order_rank->EditValue)) {
	$arwrk = $pelaku_kreatif->order_rank->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->order_rank->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->created_at->Visible) { // created_at ?>
	<div id="r_created_at" class="form-group">
		<label for="x_created_at" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_created_at"><?php echo $pelaku_kreatif->created_at->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_created_at" id="z_created_at" value="="></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->created_at->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_created_at">
<input type="text" data-field="x_created_at" name="x_created_at" id="x_created_at" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->created_at->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->created_at->EditValue ?>"<?php echo $pelaku_kreatif->created_at->EditAttributes() ?>>
<?php if (!$pelaku_kreatif->created_at->ReadOnly && !$pelaku_kreatif->created_at->Disabled && @$pelaku_kreatif->created_at->EditAttrs["readonly"] == "" && @$pelaku_kreatif->created_at->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fpelaku_kreatifsearch", "x_created_at", "%d/%m/%Y");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->role->Visible) { // role ?>
	<div id="r_role" class="form-group">
		<label for="x_role" class="<?php echo $pelaku_kreatif_search->SearchLabelClass ?>"><span id="elh_pelaku_kreatif_role"><?php echo $pelaku_kreatif->role->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_role" id="z_role" value="="></p>
		</label>
		<div class="<?php echo $pelaku_kreatif_search->SearchRightColumnClass ?>"><div<?php echo $pelaku_kreatif->role->CellAttributes() ?>>
			<span id="el_pelaku_kreatif_role">
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn()) { // Non system admin ?>
<p class="form-control-static"><?php echo $pelaku_kreatif->role->EditValue ?></p>
<?php } else { ?>
<select data-field="x_role" id="x_role" name="x_role"<?php echo $pelaku_kreatif->role->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->role->EditValue)) {
	$arwrk = $pelaku_kreatif->role->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->role->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$pelaku_kreatif_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fpelaku_kreatifsearch.Init();
</script>
<?php
$pelaku_kreatif_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pelaku_kreatif_search->Page_Terminate();
?>
