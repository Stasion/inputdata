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

$pelaku_kreatif_list = NULL; // Initialize page object first

class cpelaku_kreatif_list extends cpelaku_kreatif {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'pelaku_kreatif';

	// Page object name
	var $PageObjName = 'pelaku_kreatif_list';

	// Grid form hidden field names
	var $FormName = 'fpelaku_kreatiflist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "pelaku_kreatifadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "pelaku_kreatifdelete.php";
		$this->MultiUpdateUrl = "pelaku_kreatifupdate.php";

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pelaku_kreatif', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("home.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate();
		}

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
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

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = EW_SELECT_LIMIT;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->id, $Default, FALSE); // id
		$this->BuildSearchSql($sWhere, $this->nama_pemilik, $Default, FALSE); // nama_pemilik
		$this->BuildSearchSql($sWhere, $this->nama_industri_kreatif, $Default, FALSE); // nama_industri_kreatif
		$this->BuildSearchSql($sWhere, $this->_email, $Default, FALSE); // email
		$this->BuildSearchSql($sWhere, $this->password, $Default, FALSE); // password
		$this->BuildSearchSql($sWhere, $this->deskripsi_industri_kreatif, $Default, FALSE); // deskripsi_industri_kreatif
		$this->BuildSearchSql($sWhere, $this->alamat, $Default, FALSE); // alamat
		$this->BuildSearchSql($sWhere, $this->no_telp, $Default, FALSE); // no_telp
		$this->BuildSearchSql($sWhere, $this->image, $Default, FALSE); // image
		$this->BuildSearchSql($sWhere, $this->website, $Default, FALSE); // website
		$this->BuildSearchSql($sWhere, $this->sektor_industri_kreatif, $Default, FALSE); // sektor_industri_kreatif
		$this->BuildSearchSql($sWhere, $this->order_rank, $Default, FALSE); // order_rank
		$this->BuildSearchSql($sWhere, $this->created_at, $Default, FALSE); // created_at
		$this->BuildSearchSql($sWhere, $this->role, $Default, FALSE); // role

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->id->AdvancedSearch->Save(); // id
			$this->nama_pemilik->AdvancedSearch->Save(); // nama_pemilik
			$this->nama_industri_kreatif->AdvancedSearch->Save(); // nama_industri_kreatif
			$this->_email->AdvancedSearch->Save(); // email
			$this->password->AdvancedSearch->Save(); // password
			$this->deskripsi_industri_kreatif->AdvancedSearch->Save(); // deskripsi_industri_kreatif
			$this->alamat->AdvancedSearch->Save(); // alamat
			$this->no_telp->AdvancedSearch->Save(); // no_telp
			$this->image->AdvancedSearch->Save(); // image
			$this->website->AdvancedSearch->Save(); // website
			$this->sektor_industri_kreatif->AdvancedSearch->Save(); // sektor_industri_kreatif
			$this->order_rank->AdvancedSearch->Save(); // order_rank
			$this->created_at->AdvancedSearch->Save(); // created_at
			$this->role->AdvancedSearch->Save(); // role
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->nama_pemilik, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->nama_industri_kreatif, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->_email, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->password, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->deskripsi_industri_kreatif, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->alamat, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->no_telp, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->image, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->website, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $arKeywords, $type) {
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$sCond = $sDefCond;
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if (EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace(EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
						$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));
				$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->id->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->nama_pemilik->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->nama_industri_kreatif->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_email->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->password->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->deskripsi_industri_kreatif->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->alamat->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->no_telp->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->image->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->website->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->sektor_industri_kreatif->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->order_rank->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->created_at->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->role->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->id->AdvancedSearch->UnsetSession();
		$this->nama_pemilik->AdvancedSearch->UnsetSession();
		$this->nama_industri_kreatif->AdvancedSearch->UnsetSession();
		$this->_email->AdvancedSearch->UnsetSession();
		$this->password->AdvancedSearch->UnsetSession();
		$this->deskripsi_industri_kreatif->AdvancedSearch->UnsetSession();
		$this->alamat->AdvancedSearch->UnsetSession();
		$this->no_telp->AdvancedSearch->UnsetSession();
		$this->image->AdvancedSearch->UnsetSession();
		$this->website->AdvancedSearch->UnsetSession();
		$this->sektor_industri_kreatif->AdvancedSearch->UnsetSession();
		$this->order_rank->AdvancedSearch->UnsetSession();
		$this->created_at->AdvancedSearch->UnsetSession();
		$this->role->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
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

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id); // id
			$this->UpdateSort($this->nama_pemilik); // nama_pemilik
			$this->UpdateSort($this->nama_industri_kreatif); // nama_industri_kreatif
			$this->UpdateSort($this->_email); // email
			$this->UpdateSort($this->password); // password
			$this->UpdateSort($this->no_telp); // no_telp
			$this->UpdateSort($this->website); // website
			$this->UpdateSort($this->sektor_industri_kreatif); // sektor_industri_kreatif
			$this->UpdateSort($this->order_rank); // order_rank
			$this->UpdateSort($this->created_at); // created_at
			$this->UpdateSort($this->role); // role
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->id->setSort("");
				$this->nama_pemilik->setSort("");
				$this->nama_industri_kreatif->setSort("");
				$this->_email->setSort("");
				$this->password->setSort("");
				$this->no_telp->setSort("");
				$this->website->setSort("");
				$this->sektor_industri_kreatif->setSort("");
				$this->order_rank->setSort("");
				$this->created_at->setSort("");
				$this->role->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView() && $this->ShowOptionLink('view'))
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit() && $this->ShowOptionLink('edit')) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd() && $this->ShowOptionLink('add')) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" title=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete() && $this->ShowOptionLink('delete'))
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fpelaku_kreatiflist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fpelaku_kreatiflistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"pelaku_kreatifsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		$item->Visible = TRUE;

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch())
			$this->SearchOptions->HideAllOptions();
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// id

		$this->id->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_id"]);
		if ($this->id->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->id->AdvancedSearch->SearchOperator = @$_GET["z_id"];

		// nama_pemilik
		$this->nama_pemilik->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_nama_pemilik"]);
		if ($this->nama_pemilik->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->nama_pemilik->AdvancedSearch->SearchOperator = @$_GET["z_nama_pemilik"];

		// nama_industri_kreatif
		$this->nama_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_nama_industri_kreatif"]);
		if ($this->nama_industri_kreatif->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->nama_industri_kreatif->AdvancedSearch->SearchOperator = @$_GET["z_nama_industri_kreatif"];

		// email
		$this->_email->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__email"]);
		if ($this->_email->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_email->AdvancedSearch->SearchOperator = @$_GET["z__email"];

		// password
		$this->password->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_password"]);
		if ($this->password->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->password->AdvancedSearch->SearchOperator = @$_GET["z_password"];

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_deskripsi_industri_kreatif"]);
		if ($this->deskripsi_industri_kreatif->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->deskripsi_industri_kreatif->AdvancedSearch->SearchOperator = @$_GET["z_deskripsi_industri_kreatif"];

		// alamat
		$this->alamat->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_alamat"]);
		if ($this->alamat->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->alamat->AdvancedSearch->SearchOperator = @$_GET["z_alamat"];

		// no_telp
		$this->no_telp->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_no_telp"]);
		if ($this->no_telp->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->no_telp->AdvancedSearch->SearchOperator = @$_GET["z_no_telp"];

		// image
		$this->image->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_image"]);
		if ($this->image->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->image->AdvancedSearch->SearchOperator = @$_GET["z_image"];

		// website
		$this->website->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_website"]);
		if ($this->website->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->website->AdvancedSearch->SearchOperator = @$_GET["z_website"];

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_sektor_industri_kreatif"]);
		if ($this->sektor_industri_kreatif->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->sektor_industri_kreatif->AdvancedSearch->SearchOperator = @$_GET["z_sektor_industri_kreatif"];

		// order_rank
		$this->order_rank->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_order_rank"]);
		if ($this->order_rank->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->order_rank->AdvancedSearch->SearchOperator = @$_GET["z_order_rank"];

		// created_at
		$this->created_at->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_created_at"]);
		if ($this->created_at->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->created_at->AdvancedSearch->SearchOperator = @$_GET["z_created_at"];

		// role
		$this->role->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_role"]);
		if ($this->role->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->role->AdvancedSearch->SearchOperator = @$_GET["z_role"];
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Load List page SQL
		$sSql = $this->SelectSQL();

		// Load recordset
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
		$conn->raiseErrorFn = '';

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id->setDbValue($rs->fields('id'));
		$this->nama_pemilik->setDbValue($rs->fields('nama_pemilik'));
		$this->nama_industri_kreatif->setDbValue($rs->fields('nama_industri_kreatif'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->password->setDbValue($rs->fields('password'));
		$this->deskripsi_industri_kreatif->setDbValue($rs->fields('deskripsi_industri_kreatif'));
		$this->alamat->setDbValue($rs->fields('alamat'));
		$this->no_telp->setDbValue($rs->fields('no_telp'));
		$this->image->Upload->DbValue = $rs->fields('image');
		$this->image->CurrentValue = $this->image->Upload->DbValue;
		$this->website->setDbValue($rs->fields('website'));
		$this->sektor_industri_kreatif->setDbValue($rs->fields('sektor_industri_kreatif'));
		$this->order_rank->setDbValue($rs->fields('order_rank'));
		$this->created_at->setDbValue($rs->fields('created_at'));
		$this->role->setDbValue($rs->fields('role'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->nama_pemilik->DbValue = $row['nama_pemilik'];
		$this->nama_industri_kreatif->DbValue = $row['nama_industri_kreatif'];
		$this->_email->DbValue = $row['email'];
		$this->password->DbValue = $row['password'];
		$this->deskripsi_industri_kreatif->DbValue = $row['deskripsi_industri_kreatif'];
		$this->alamat->DbValue = $row['alamat'];
		$this->no_telp->DbValue = $row['no_telp'];
		$this->image->Upload->DbValue = $row['image'];
		$this->website->DbValue = $row['website'];
		$this->sektor_industri_kreatif->DbValue = $row['sektor_industri_kreatif'];
		$this->order_rank->DbValue = $row['order_rank'];
		$this->created_at->DbValue = $row['created_at'];
		$this->role->DbValue = $row['role'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id")) <> "")
			$this->id->CurrentValue = $this->getKey("id"); // id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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

			// no_telp
			$this->no_telp->ViewValue = $this->no_telp->CurrentValue;
			$this->no_telp->ViewCustomAttributes = "";

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

			// no_telp
			$this->no_telp->LinkCustomAttributes = "";
			$this->no_telp->HrefValue = "";
			$this->no_telp->TooltipValue = "";

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

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = FALSE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_pelaku_kreatif\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_pelaku_kreatif',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fpelaku_kreatiflist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = FALSE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Call Page Exported server event
		$this->Page_Exported();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$Doc->Export();
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->id->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

	    //$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($pelaku_kreatif_list)) $pelaku_kreatif_list = new cpelaku_kreatif_list();

// Page init
$pelaku_kreatif_list->Page_Init();

// Page main
$pelaku_kreatif_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pelaku_kreatif_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<script type="text/javascript">

// Page object
var pelaku_kreatif_list = new ew_Page("pelaku_kreatif_list");
pelaku_kreatif_list.PageID = "list"; // Page ID
var EW_PAGE_ID = pelaku_kreatif_list.PageID; // For backward compatibility

// Form object
var fpelaku_kreatiflist = new ew_Form("fpelaku_kreatiflist");
fpelaku_kreatiflist.FormKeyCountName = '<?php echo $pelaku_kreatif_list->FormKeyCountName ?>';

// Form_CustomValidate event
fpelaku_kreatiflist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpelaku_kreatiflist.ValidateRequired = true;
<?php } else { ?>
fpelaku_kreatiflist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpelaku_kreatiflist.Lists["x_sektor_industri_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_sektor","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fpelaku_kreatiflistsrch = new ew_Form("fpelaku_kreatiflistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<div class="ewToolbar">
<?php if ($pelaku_kreatif->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($pelaku_kreatif_list->TotalRecs > 0 && $pelaku_kreatif_list->ExportOptions->Visible()) { ?>
<?php $pelaku_kreatif_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($pelaku_kreatif_list->SearchOptions->Visible()) { ?>
<?php $pelaku_kreatif_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		if ($pelaku_kreatif_list->TotalRecs <= 0)
			$pelaku_kreatif_list->TotalRecs = $pelaku_kreatif->SelectRecordCount();
	} else {
		if (!$pelaku_kreatif_list->Recordset && ($pelaku_kreatif_list->Recordset = $pelaku_kreatif_list->LoadRecordset()))
			$pelaku_kreatif_list->TotalRecs = $pelaku_kreatif_list->Recordset->RecordCount();
	}
	$pelaku_kreatif_list->StartRec = 1;
	if ($pelaku_kreatif_list->DisplayRecs <= 0 || ($pelaku_kreatif->Export <> "" && $pelaku_kreatif->ExportAll)) // Display all records
		$pelaku_kreatif_list->DisplayRecs = $pelaku_kreatif_list->TotalRecs;
	if (!($pelaku_kreatif->Export <> "" && $pelaku_kreatif->ExportAll))
		$pelaku_kreatif_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$pelaku_kreatif_list->Recordset = $pelaku_kreatif_list->LoadRecordset($pelaku_kreatif_list->StartRec-1, $pelaku_kreatif_list->DisplayRecs);

	// Set no record found message
	if ($pelaku_kreatif->CurrentAction == "" && $pelaku_kreatif_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$pelaku_kreatif_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($pelaku_kreatif_list->SearchWhere == "0=101")
			$pelaku_kreatif_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$pelaku_kreatif_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$pelaku_kreatif_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($pelaku_kreatif->Export == "" && $pelaku_kreatif->CurrentAction == "") { ?>
<form name="fpelaku_kreatiflistsrch" id="fpelaku_kreatiflistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($pelaku_kreatif_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="fpelaku_kreatiflistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="pelaku_kreatif">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($pelaku_kreatif_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($pelaku_kreatif_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $pelaku_kreatif_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($pelaku_kreatif_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($pelaku_kreatif_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($pelaku_kreatif_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($pelaku_kreatif_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $pelaku_kreatif_list->ShowPageHeader(); ?>
<?php
$pelaku_kreatif_list->ShowMessage();
?>
<?php if ($pelaku_kreatif_list->TotalRecs > 0 || $pelaku_kreatif->CurrentAction <> "") { ?>
<div class="ewGrid">
<form name="fpelaku_kreatiflist" id="fpelaku_kreatiflist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pelaku_kreatif_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pelaku_kreatif_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pelaku_kreatif">
<div id="gmp_pelaku_kreatif" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($pelaku_kreatif_list->TotalRecs > 0) { ?>
<table id="tbl_pelaku_kreatiflist" class="table ewTable">
<?php echo $pelaku_kreatif->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$pelaku_kreatif_list->RenderListOptions();

// Render list options (header, left)
$pelaku_kreatif_list->ListOptions->Render("header", "left");
?>
<?php if ($pelaku_kreatif->id->Visible) { // id ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->id) == "") { ?>
		<th data-name="id"><div id="elh_pelaku_kreatif_id" class="pelaku_kreatif_id"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->id) ?>',1);"><div id="elh_pelaku_kreatif_id" class="pelaku_kreatif_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->nama_pemilik->Visible) { // nama_pemilik ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->nama_pemilik) == "") { ?>
		<th data-name="nama_pemilik"><div id="elh_pelaku_kreatif_nama_pemilik" class="pelaku_kreatif_nama_pemilik"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->nama_pemilik->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="nama_pemilik"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->nama_pemilik) ?>',1);"><div id="elh_pelaku_kreatif_nama_pemilik" class="pelaku_kreatif_nama_pemilik">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->nama_pemilik->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->nama_pemilik->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->nama_pemilik->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->nama_industri_kreatif->Visible) { // nama_industri_kreatif ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->nama_industri_kreatif) == "") { ?>
		<th data-name="nama_industri_kreatif"><div id="elh_pelaku_kreatif_nama_industri_kreatif" class="pelaku_kreatif_nama_industri_kreatif"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->nama_industri_kreatif->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="nama_industri_kreatif"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->nama_industri_kreatif) ?>',1);"><div id="elh_pelaku_kreatif_nama_industri_kreatif" class="pelaku_kreatif_nama_industri_kreatif">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->nama_industri_kreatif->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->nama_industri_kreatif->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->nama_industri_kreatif->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->_email->Visible) { // email ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->_email) == "") { ?>
		<th data-name="_email"><div id="elh_pelaku_kreatif__email" class="pelaku_kreatif__email"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->_email->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_email"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->_email) ?>',1);"><div id="elh_pelaku_kreatif__email" class="pelaku_kreatif__email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->_email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->_email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->_email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->password->Visible) { // password ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->password) == "") { ?>
		<th data-name="password"><div id="elh_pelaku_kreatif_password" class="pelaku_kreatif_password"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->password->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="password"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->password) ?>',1);"><div id="elh_pelaku_kreatif_password" class="pelaku_kreatif_password">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->password->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->password->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->password->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->no_telp->Visible) { // no_telp ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->no_telp) == "") { ?>
		<th data-name="no_telp"><div id="elh_pelaku_kreatif_no_telp" class="pelaku_kreatif_no_telp"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->no_telp->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="no_telp"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->no_telp) ?>',1);"><div id="elh_pelaku_kreatif_no_telp" class="pelaku_kreatif_no_telp">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->no_telp->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->no_telp->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->no_telp->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->website->Visible) { // website ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->website) == "") { ?>
		<th data-name="website"><div id="elh_pelaku_kreatif_website" class="pelaku_kreatif_website"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->website->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="website"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->website) ?>',1);"><div id="elh_pelaku_kreatif_website" class="pelaku_kreatif_website">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->website->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->website->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->website->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->sektor_industri_kreatif->Visible) { // sektor_industri_kreatif ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->sektor_industri_kreatif) == "") { ?>
		<th data-name="sektor_industri_kreatif"><div id="elh_pelaku_kreatif_sektor_industri_kreatif" class="pelaku_kreatif_sektor_industri_kreatif"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->sektor_industri_kreatif->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="sektor_industri_kreatif"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->sektor_industri_kreatif) ?>',1);"><div id="elh_pelaku_kreatif_sektor_industri_kreatif" class="pelaku_kreatif_sektor_industri_kreatif">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->sektor_industri_kreatif->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->sektor_industri_kreatif->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->sektor_industri_kreatif->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->order_rank->Visible) { // order_rank ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->order_rank) == "") { ?>
		<th data-name="order_rank"><div id="elh_pelaku_kreatif_order_rank" class="pelaku_kreatif_order_rank"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->order_rank->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="order_rank"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->order_rank) ?>',1);"><div id="elh_pelaku_kreatif_order_rank" class="pelaku_kreatif_order_rank">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->order_rank->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->order_rank->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->order_rank->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->created_at->Visible) { // created_at ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->created_at) == "") { ?>
		<th data-name="created_at"><div id="elh_pelaku_kreatif_created_at" class="pelaku_kreatif_created_at"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->created_at->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="created_at"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->created_at) ?>',1);"><div id="elh_pelaku_kreatif_created_at" class="pelaku_kreatif_created_at">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->created_at->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->created_at->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->created_at->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($pelaku_kreatif->role->Visible) { // role ?>
	<?php if ($pelaku_kreatif->SortUrl($pelaku_kreatif->role) == "") { ?>
		<th data-name="role"><div id="elh_pelaku_kreatif_role" class="pelaku_kreatif_role"><div class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->role->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="role"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pelaku_kreatif->SortUrl($pelaku_kreatif->role) ?>',1);"><div id="elh_pelaku_kreatif_role" class="pelaku_kreatif_role">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pelaku_kreatif->role->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pelaku_kreatif->role->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pelaku_kreatif->role->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$pelaku_kreatif_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($pelaku_kreatif->ExportAll && $pelaku_kreatif->Export <> "") {
	$pelaku_kreatif_list->StopRec = $pelaku_kreatif_list->TotalRecs;
} else {

	// Set the last record to display
	if ($pelaku_kreatif_list->TotalRecs > $pelaku_kreatif_list->StartRec + $pelaku_kreatif_list->DisplayRecs - 1)
		$pelaku_kreatif_list->StopRec = $pelaku_kreatif_list->StartRec + $pelaku_kreatif_list->DisplayRecs - 1;
	else
		$pelaku_kreatif_list->StopRec = $pelaku_kreatif_list->TotalRecs;
}
$pelaku_kreatif_list->RecCnt = $pelaku_kreatif_list->StartRec - 1;
if ($pelaku_kreatif_list->Recordset && !$pelaku_kreatif_list->Recordset->EOF) {
	$pelaku_kreatif_list->Recordset->MoveFirst();
	$bSelectLimit = EW_SELECT_LIMIT;
	if (!$bSelectLimit && $pelaku_kreatif_list->StartRec > 1)
		$pelaku_kreatif_list->Recordset->Move($pelaku_kreatif_list->StartRec - 1);
} elseif (!$pelaku_kreatif->AllowAddDeleteRow && $pelaku_kreatif_list->StopRec == 0) {
	$pelaku_kreatif_list->StopRec = $pelaku_kreatif->GridAddRowCount;
}

// Initialize aggregate
$pelaku_kreatif->RowType = EW_ROWTYPE_AGGREGATEINIT;
$pelaku_kreatif->ResetAttrs();
$pelaku_kreatif_list->RenderRow();
while ($pelaku_kreatif_list->RecCnt < $pelaku_kreatif_list->StopRec) {
	$pelaku_kreatif_list->RecCnt++;
	if (intval($pelaku_kreatif_list->RecCnt) >= intval($pelaku_kreatif_list->StartRec)) {
		$pelaku_kreatif_list->RowCnt++;

		// Set up key count
		$pelaku_kreatif_list->KeyCount = $pelaku_kreatif_list->RowIndex;

		// Init row class and style
		$pelaku_kreatif->ResetAttrs();
		$pelaku_kreatif->CssClass = "";
		if ($pelaku_kreatif->CurrentAction == "gridadd") {
		} else {
			$pelaku_kreatif_list->LoadRowValues($pelaku_kreatif_list->Recordset); // Load row values
		}
		$pelaku_kreatif->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$pelaku_kreatif->RowAttrs = array_merge($pelaku_kreatif->RowAttrs, array('data-rowindex'=>$pelaku_kreatif_list->RowCnt, 'id'=>'r' . $pelaku_kreatif_list->RowCnt . '_pelaku_kreatif', 'data-rowtype'=>$pelaku_kreatif->RowType));

		// Render row
		$pelaku_kreatif_list->RenderRow();

		// Render list options
		$pelaku_kreatif_list->RenderListOptions();
?>
	<tr<?php echo $pelaku_kreatif->RowAttributes() ?>>
<?php

// Render list options (body, left)
$pelaku_kreatif_list->ListOptions->Render("body", "left", $pelaku_kreatif_list->RowCnt);
?>
	<?php if ($pelaku_kreatif->id->Visible) { // id ?>
		<td data-name="id"<?php echo $pelaku_kreatif->id->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->id->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->id->ListViewValue() ?></span>
<a id="<?php echo $pelaku_kreatif_list->PageObjName . "_row_" . $pelaku_kreatif_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($pelaku_kreatif->nama_pemilik->Visible) { // nama_pemilik ?>
		<td data-name="nama_pemilik"<?php echo $pelaku_kreatif->nama_pemilik->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->nama_pemilik->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->nama_pemilik->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->nama_industri_kreatif->Visible) { // nama_industri_kreatif ?>
		<td data-name="nama_industri_kreatif"<?php echo $pelaku_kreatif->nama_industri_kreatif->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->nama_industri_kreatif->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->nama_industri_kreatif->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->_email->Visible) { // email ?>
		<td data-name="_email"<?php echo $pelaku_kreatif->_email->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->_email->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->_email->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->password->Visible) { // password ?>
		<td data-name="password"<?php echo $pelaku_kreatif->password->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->password->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->password->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->no_telp->Visible) { // no_telp ?>
		<td data-name="no_telp"<?php echo $pelaku_kreatif->no_telp->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->no_telp->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->no_telp->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->website->Visible) { // website ?>
		<td data-name="website"<?php echo $pelaku_kreatif->website->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->website->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->website->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->sektor_industri_kreatif->Visible) { // sektor_industri_kreatif ?>
		<td data-name="sektor_industri_kreatif"<?php echo $pelaku_kreatif->sektor_industri_kreatif->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->sektor_industri_kreatif->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->sektor_industri_kreatif->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->order_rank->Visible) { // order_rank ?>
		<td data-name="order_rank"<?php echo $pelaku_kreatif->order_rank->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->order_rank->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->order_rank->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->created_at->Visible) { // created_at ?>
		<td data-name="created_at"<?php echo $pelaku_kreatif->created_at->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->created_at->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->created_at->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pelaku_kreatif->role->Visible) { // role ?>
		<td data-name="role"<?php echo $pelaku_kreatif->role->CellAttributes() ?>>
<span<?php echo $pelaku_kreatif->role->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->role->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$pelaku_kreatif_list->ListOptions->Render("body", "right", $pelaku_kreatif_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($pelaku_kreatif->CurrentAction <> "gridadd")
		$pelaku_kreatif_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($pelaku_kreatif->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($pelaku_kreatif_list->Recordset)
	$pelaku_kreatif_list->Recordset->Close();
?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($pelaku_kreatif->CurrentAction <> "gridadd" && $pelaku_kreatif->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($pelaku_kreatif_list->Pager)) $pelaku_kreatif_list->Pager = new cPrevNextPager($pelaku_kreatif_list->StartRec, $pelaku_kreatif_list->DisplayRecs, $pelaku_kreatif_list->TotalRecs) ?>
<?php if ($pelaku_kreatif_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($pelaku_kreatif_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $pelaku_kreatif_list->PageUrl() ?>start=<?php echo $pelaku_kreatif_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($pelaku_kreatif_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $pelaku_kreatif_list->PageUrl() ?>start=<?php echo $pelaku_kreatif_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $pelaku_kreatif_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($pelaku_kreatif_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $pelaku_kreatif_list->PageUrl() ?>start=<?php echo $pelaku_kreatif_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($pelaku_kreatif_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $pelaku_kreatif_list->PageUrl() ?>start=<?php echo $pelaku_kreatif_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $pelaku_kreatif_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $pelaku_kreatif_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $pelaku_kreatif_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $pelaku_kreatif_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($pelaku_kreatif_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($pelaku_kreatif_list->TotalRecs == 0 && $pelaku_kreatif->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($pelaku_kreatif_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<script type="text/javascript">
fpelaku_kreatiflistsrch.Init();
fpelaku_kreatiflist.Init();
</script>
<?php } ?>
<?php
$pelaku_kreatif_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($pelaku_kreatif->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$pelaku_kreatif_list->Page_Terminate();
?>
