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

$pelaku_kreatif_view = NULL; // Initialize page object first

class cpelaku_kreatif_view extends cpelaku_kreatif {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'pelaku_kreatif';

	// Page object name
	var $PageObjName = 'pelaku_kreatif_view';

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
		$KeyUrl = "";
		if (@$_GET["id"] <> "") {
			$this->RecKey["id"] = $_GET["id"];
			$KeyUrl .= "&amp;id=" . urlencode($this->RecKey["id"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pelaku_kreatif', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["id"] <> "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->RecKey["id"] = $this->id->QueryStringValue;
			} else {
				$sReturnUrl = "pelaku_kreatiflist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "pelaku_kreatiflist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "pelaku_kreatiflist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit()&& $this->ShowOptionLink('edit'));

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"ewAction ewCopy\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageCopyLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "" && $Security->CanAdd() && $this->ShowOptionLink('add'));

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete() && $this->ShowOptionLink('delete'));

		// Set up action default
		$option = &$options["action"];
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
		$option->UseImageAndText = TRUE;
		$option->UseDropDownButton = FALSE;
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
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

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
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
		$Breadcrumb->Add("list", $this->TableVar, "pelaku_kreatiflist.php", "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
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
if (!isset($pelaku_kreatif_view)) $pelaku_kreatif_view = new cpelaku_kreatif_view();

// Page init
$pelaku_kreatif_view->Page_Init();

// Page main
$pelaku_kreatif_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pelaku_kreatif_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pelaku_kreatif_view = new ew_Page("pelaku_kreatif_view");
pelaku_kreatif_view.PageID = "view"; // Page ID
var EW_PAGE_ID = pelaku_kreatif_view.PageID; // For backward compatibility

// Form object
var fpelaku_kreatifview = new ew_Form("fpelaku_kreatifview");

// Form_CustomValidate event
fpelaku_kreatifview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpelaku_kreatifview.ValidateRequired = true;
<?php } else { ?>
fpelaku_kreatifview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpelaku_kreatifview.Lists["x_sektor_industri_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_sektor","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php $pelaku_kreatif_view->ExportOptions->Render("body") ?>
<?php
	foreach ($pelaku_kreatif_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $pelaku_kreatif_view->ShowPageHeader(); ?>
<?php
$pelaku_kreatif_view->ShowMessage();
?>
<form name="fpelaku_kreatifview" id="fpelaku_kreatifview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pelaku_kreatif_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pelaku_kreatif_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pelaku_kreatif">
<table class="table table-bordered table-striped ewViewTable">
<?php if ($pelaku_kreatif->id->Visible) { // id ?>
	<tr id="r_id">
		<td><span id="elh_pelaku_kreatif_id"><?php echo $pelaku_kreatif->id->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->id->CellAttributes() ?>>
<span id="el_pelaku_kreatif_id" class="form-group">
<span<?php echo $pelaku_kreatif->id->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->nama_pemilik->Visible) { // nama_pemilik ?>
	<tr id="r_nama_pemilik">
		<td><span id="elh_pelaku_kreatif_nama_pemilik"><?php echo $pelaku_kreatif->nama_pemilik->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->nama_pemilik->CellAttributes() ?>>
<span id="el_pelaku_kreatif_nama_pemilik" class="form-group">
<span<?php echo $pelaku_kreatif->nama_pemilik->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->nama_pemilik->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->nama_industri_kreatif->Visible) { // nama_industri_kreatif ?>
	<tr id="r_nama_industri_kreatif">
		<td><span id="elh_pelaku_kreatif_nama_industri_kreatif"><?php echo $pelaku_kreatif->nama_industri_kreatif->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->nama_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_nama_industri_kreatif" class="form-group">
<span<?php echo $pelaku_kreatif->nama_industri_kreatif->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->nama_industri_kreatif->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->_email->Visible) { // email ?>
	<tr id="r__email">
		<td><span id="elh_pelaku_kreatif__email"><?php echo $pelaku_kreatif->_email->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->_email->CellAttributes() ?>>
<span id="el_pelaku_kreatif__email" class="form-group">
<span<?php echo $pelaku_kreatif->_email->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->_email->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->password->Visible) { // password ?>
	<tr id="r_password">
		<td><span id="elh_pelaku_kreatif_password"><?php echo $pelaku_kreatif->password->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->password->CellAttributes() ?>>
<span id="el_pelaku_kreatif_password" class="form-group">
<span<?php echo $pelaku_kreatif->password->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->password->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->deskripsi_industri_kreatif->Visible) { // deskripsi_industri_kreatif ?>
	<tr id="r_deskripsi_industri_kreatif">
		<td><span id="elh_pelaku_kreatif_deskripsi_industri_kreatif"><?php echo $pelaku_kreatif->deskripsi_industri_kreatif->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_deskripsi_industri_kreatif" class="form-group">
<span<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->alamat->Visible) { // alamat ?>
	<tr id="r_alamat">
		<td><span id="elh_pelaku_kreatif_alamat"><?php echo $pelaku_kreatif->alamat->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->alamat->CellAttributes() ?>>
<span id="el_pelaku_kreatif_alamat" class="form-group">
<span<?php echo $pelaku_kreatif->alamat->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->alamat->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->no_telp->Visible) { // no_telp ?>
	<tr id="r_no_telp">
		<td><span id="elh_pelaku_kreatif_no_telp"><?php echo $pelaku_kreatif->no_telp->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->no_telp->CellAttributes() ?>>
<span id="el_pelaku_kreatif_no_telp" class="form-group">
<span<?php echo $pelaku_kreatif->no_telp->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->no_telp->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->image->Visible) { // image ?>
	<tr id="r_image">
		<td><span id="elh_pelaku_kreatif_image"><?php echo $pelaku_kreatif->image->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->image->CellAttributes() ?>>
<span id="el_pelaku_kreatif_image" class="form-group">
<span>
<?php echo ew_GetFileViewTag($pelaku_kreatif->image, $pelaku_kreatif->image->ViewValue) ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->website->Visible) { // website ?>
	<tr id="r_website">
		<td><span id="elh_pelaku_kreatif_website"><?php echo $pelaku_kreatif->website->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->website->CellAttributes() ?>>
<span id="el_pelaku_kreatif_website" class="form-group">
<span<?php echo $pelaku_kreatif->website->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->website->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->sektor_industri_kreatif->Visible) { // sektor_industri_kreatif ?>
	<tr id="r_sektor_industri_kreatif">
		<td><span id="elh_pelaku_kreatif_sektor_industri_kreatif"><?php echo $pelaku_kreatif->sektor_industri_kreatif->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->sektor_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_sektor_industri_kreatif" class="form-group">
<span<?php echo $pelaku_kreatif->sektor_industri_kreatif->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->sektor_industri_kreatif->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->order_rank->Visible) { // order_rank ?>
	<tr id="r_order_rank">
		<td><span id="elh_pelaku_kreatif_order_rank"><?php echo $pelaku_kreatif->order_rank->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->order_rank->CellAttributes() ?>>
<span id="el_pelaku_kreatif_order_rank" class="form-group">
<span<?php echo $pelaku_kreatif->order_rank->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->order_rank->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->created_at->Visible) { // created_at ?>
	<tr id="r_created_at">
		<td><span id="elh_pelaku_kreatif_created_at"><?php echo $pelaku_kreatif->created_at->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->created_at->CellAttributes() ?>>
<span id="el_pelaku_kreatif_created_at" class="form-group">
<span<?php echo $pelaku_kreatif->created_at->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->created_at->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($pelaku_kreatif->role->Visible) { // role ?>
	<tr id="r_role">
		<td><span id="elh_pelaku_kreatif_role"><?php echo $pelaku_kreatif->role->FldCaption() ?></span></td>
		<td<?php echo $pelaku_kreatif->role->CellAttributes() ?>>
<span id="el_pelaku_kreatif_role" class="form-group">
<span<?php echo $pelaku_kreatif->role->ViewAttributes() ?>>
<?php echo $pelaku_kreatif->role->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</form>
<script type="text/javascript">
fpelaku_kreatifview.Init();
</script>
<?php
$pelaku_kreatif_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pelaku_kreatif_view->Page_Terminate();
?>
