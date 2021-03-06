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

$pelaku_kreatif_edit = NULL; // Initialize page object first

class cpelaku_kreatif_edit extends cpelaku_kreatif {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'pelaku_kreatif';

	// Page object name
	var $PageObjName = 'pelaku_kreatif_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id->CurrentValue == "")
			$this->Page_Terminate("pelaku_kreatiflist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("pelaku_kreatiflist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->image->Upload->Index = $objForm->Index;
		$this->image->Upload->UploadFile();
		$this->image->CurrentValue = $this->image->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->nama_pemilik->FldIsDetailKey) {
			$this->nama_pemilik->setFormValue($objForm->GetValue("x_nama_pemilik"));
		}
		if (!$this->nama_industri_kreatif->FldIsDetailKey) {
			$this->nama_industri_kreatif->setFormValue($objForm->GetValue("x_nama_industri_kreatif"));
		}
		if (!$this->_email->FldIsDetailKey) {
			$this->_email->setFormValue($objForm->GetValue("x__email"));
		}
		if (!$this->password->FldIsDetailKey) {
			$this->password->setFormValue($objForm->GetValue("x_password"));
		}
		if (!$this->deskripsi_industri_kreatif->FldIsDetailKey) {
			$this->deskripsi_industri_kreatif->setFormValue($objForm->GetValue("x_deskripsi_industri_kreatif"));
		}
		if (!$this->alamat->FldIsDetailKey) {
			$this->alamat->setFormValue($objForm->GetValue("x_alamat"));
		}
		if (!$this->no_telp->FldIsDetailKey) {
			$this->no_telp->setFormValue($objForm->GetValue("x_no_telp"));
		}
		if (!$this->website->FldIsDetailKey) {
			$this->website->setFormValue($objForm->GetValue("x_website"));
		}
		if (!$this->sektor_industri_kreatif->FldIsDetailKey) {
			$this->sektor_industri_kreatif->setFormValue($objForm->GetValue("x_sektor_industri_kreatif"));
		}
		if (!$this->order_rank->FldIsDetailKey) {
			$this->order_rank->setFormValue($objForm->GetValue("x_order_rank"));
		}
		if (!$this->created_at->FldIsDetailKey) {
			$this->created_at->setFormValue($objForm->GetValue("x_created_at"));
			$this->created_at->CurrentValue = ew_UnFormatDateTime($this->created_at->CurrentValue, 7);
		}
		if (!$this->role->FldIsDetailKey) {
			$this->role->setFormValue($objForm->GetValue("x_role"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
		$this->nama_pemilik->CurrentValue = $this->nama_pemilik->FormValue;
		$this->nama_industri_kreatif->CurrentValue = $this->nama_industri_kreatif->FormValue;
		$this->_email->CurrentValue = $this->_email->FormValue;
		$this->password->CurrentValue = $this->password->FormValue;
		$this->deskripsi_industri_kreatif->CurrentValue = $this->deskripsi_industri_kreatif->FormValue;
		$this->alamat->CurrentValue = $this->alamat->FormValue;
		$this->no_telp->CurrentValue = $this->no_telp->FormValue;
		$this->website->CurrentValue = $this->website->FormValue;
		$this->sektor_industri_kreatif->CurrentValue = $this->sektor_industri_kreatif->FormValue;
		$this->order_rank->CurrentValue = $this->order_rank->FormValue;
		$this->created_at->CurrentValue = $this->created_at->FormValue;
		$this->created_at->CurrentValue = ew_UnFormatDateTime($this->created_at->CurrentValue, 7);
		$this->role->CurrentValue = $this->role->FormValue;
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

		// Check if valid user id
		if ($res) {
			$res = $this->ShowOptionLink('edit');
			if (!$res) {
				$sUserIdMsg = $Language->Phrase("NoPermission");
				$this->setFailureMessage($sUserIdMsg);
			}
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

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
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->image);

			// website
			$this->website->EditAttrs["class"] = "form-control";
			$this->website->EditCustomAttributes = "";
			$this->website->EditValue = ew_HtmlEncode($this->website->CurrentValue);
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

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// nama_pemilik
			$this->nama_pemilik->HrefValue = "";

			// nama_industri_kreatif
			$this->nama_industri_kreatif->HrefValue = "";

			// email
			$this->_email->HrefValue = "";

			// password
			$this->password->HrefValue = "";

			// deskripsi_industri_kreatif
			$this->deskripsi_industri_kreatif->HrefValue = "";

			// alamat
			$this->alamat->HrefValue = "";

			// no_telp
			$this->no_telp->HrefValue = "";

			// image
			if (!ew_Empty($this->image->Upload->DbValue)) {
				$this->image->HrefValue = ew_UploadPathEx(FALSE, $this->image->UploadPath) . $this->image->Upload->DbValue; // Add prefix/suffix
				$this->image->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->image->HrefValue = ew_ConvertFullUrl($this->image->HrefValue);
			} else {
				$this->image->HrefValue = "";
			}
			$this->image->HrefValue2 = $this->image->UploadPath . $this->image->Upload->DbValue;

			// website
			$this->website->HrefValue = "";

			// sektor_industri_kreatif
			$this->sektor_industri_kreatif->HrefValue = "";

			// order_rank
			$this->order_rank->HrefValue = "";

			// created_at
			$this->created_at->HrefValue = "";

			// role
			$this->role->HrefValue = "";
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

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->nama_pemilik->FldIsDetailKey && !is_null($this->nama_pemilik->FormValue) && $this->nama_pemilik->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->nama_pemilik->FldCaption(), $this->nama_pemilik->ReqErrMsg));
		}
		if (!$this->nama_industri_kreatif->FldIsDetailKey && !is_null($this->nama_industri_kreatif->FormValue) && $this->nama_industri_kreatif->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->nama_industri_kreatif->FldCaption(), $this->nama_industri_kreatif->ReqErrMsg));
		}
		if (!$this->_email->FldIsDetailKey && !is_null($this->_email->FormValue) && $this->_email->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->_email->FldCaption(), $this->_email->ReqErrMsg));
		}
		if (!$this->password->FldIsDetailKey && !is_null($this->password->FormValue) && $this->password->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->password->FldCaption(), $this->password->ReqErrMsg));
		}
		if (!$this->deskripsi_industri_kreatif->FldIsDetailKey && !is_null($this->deskripsi_industri_kreatif->FormValue) && $this->deskripsi_industri_kreatif->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->deskripsi_industri_kreatif->FldCaption(), $this->deskripsi_industri_kreatif->ReqErrMsg));
		}
		if (!$this->alamat->FldIsDetailKey && !is_null($this->alamat->FormValue) && $this->alamat->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->alamat->FldCaption(), $this->alamat->ReqErrMsg));
		}
		if (!$this->no_telp->FldIsDetailKey && !is_null($this->no_telp->FormValue) && $this->no_telp->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->no_telp->FldCaption(), $this->no_telp->ReqErrMsg));
		}
		if ($this->image->Upload->FileName == "" && !$this->image->Upload->KeepFile) {
			ew_AddMessage($gsFormError, str_replace("%s", $this->image->FldCaption(), $this->image->ReqErrMsg));
		}
		if (!$this->sektor_industri_kreatif->FldIsDetailKey && !is_null($this->sektor_industri_kreatif->FormValue) && $this->sektor_industri_kreatif->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->sektor_industri_kreatif->FldCaption(), $this->sektor_industri_kreatif->ReqErrMsg));
		}
		if (!$this->order_rank->FldIsDetailKey && !is_null($this->order_rank->FormValue) && $this->order_rank->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->order_rank->FldCaption(), $this->order_rank->ReqErrMsg));
		}
		if (!$this->created_at->FldIsDetailKey && !is_null($this->created_at->FormValue) && $this->created_at->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->created_at->FldCaption(), $this->created_at->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->created_at->FormValue)) {
			ew_AddMessage($gsFormError, $this->created_at->FldErrMsg());
		}
		if (!$this->role->FldIsDetailKey && !is_null($this->role->FormValue) && $this->role->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->role->FldCaption(), $this->role->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// nama_pemilik
			$this->nama_pemilik->SetDbValueDef($rsnew, $this->nama_pemilik->CurrentValue, "", $this->nama_pemilik->ReadOnly);

			// nama_industri_kreatif
			$this->nama_industri_kreatif->SetDbValueDef($rsnew, $this->nama_industri_kreatif->CurrentValue, "", $this->nama_industri_kreatif->ReadOnly);

			// email
			$this->_email->SetDbValueDef($rsnew, $this->_email->CurrentValue, "", $this->_email->ReadOnly);

			// password
			$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, "", $this->password->ReadOnly || (EW_ENCRYPTED_PASSWORD && $rs->fields('password') == $this->password->CurrentValue));

			// deskripsi_industri_kreatif
			$this->deskripsi_industri_kreatif->SetDbValueDef($rsnew, $this->deskripsi_industri_kreatif->CurrentValue, "", $this->deskripsi_industri_kreatif->ReadOnly);

			// alamat
			$this->alamat->SetDbValueDef($rsnew, $this->alamat->CurrentValue, NULL, $this->alamat->ReadOnly);

			// no_telp
			$this->no_telp->SetDbValueDef($rsnew, $this->no_telp->CurrentValue, "", $this->no_telp->ReadOnly);

			// image
			if (!($this->image->ReadOnly) && !$this->image->Upload->KeepFile) {
				$this->image->Upload->DbValue = $rsold['image']; // Get original value
				if ($this->image->Upload->FileName == "") {
					$rsnew['image'] = NULL;
				} else {
					$rsnew['image'] = $this->image->Upload->FileName;
				}
			}

			// website
			$this->website->SetDbValueDef($rsnew, $this->website->CurrentValue, NULL, $this->website->ReadOnly);

			// sektor_industri_kreatif
			$this->sektor_industri_kreatif->SetDbValueDef($rsnew, $this->sektor_industri_kreatif->CurrentValue, 0, $this->sektor_industri_kreatif->ReadOnly);

			// order_rank
			$this->order_rank->SetDbValueDef($rsnew, $this->order_rank->CurrentValue, 0, $this->order_rank->ReadOnly);

			// created_at
			$this->created_at->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->created_at->CurrentValue, 7), ew_CurrentDate(), $this->created_at->ReadOnly);

			// role
			if ($Security->CanAdmin()) { // System admin
			$this->role->SetDbValueDef($rsnew, $this->role->CurrentValue, 0, $this->role->ReadOnly);
			}
			if (!$this->image->Upload->KeepFile) {
				if (!ew_Empty($this->image->Upload->Value)) {
					$rsnew['image'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->image->UploadPath), $rsnew['image']); // Get new file name
				}
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
					if (!$this->image->Upload->KeepFile) {
						if (!ew_Empty($this->image->Upload->Value)) {
							$this->image->Upload->SaveToFile($this->image->UploadPath, $rsnew['image'], TRUE);
						}
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// image
		ew_CleanUploadTempPath($this->image, $this->image->Upload->Index);
		return $EditRow;
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
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($pelaku_kreatif_edit)) $pelaku_kreatif_edit = new cpelaku_kreatif_edit();

// Page init
$pelaku_kreatif_edit->Page_Init();

// Page main
$pelaku_kreatif_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pelaku_kreatif_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pelaku_kreatif_edit = new ew_Page("pelaku_kreatif_edit");
pelaku_kreatif_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = pelaku_kreatif_edit.PageID; // For backward compatibility

// Form object
var fpelaku_kreatifedit = new ew_Form("fpelaku_kreatifedit");

// Validate form
fpelaku_kreatifedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_nama_pemilik");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->nama_pemilik->FldCaption(), $pelaku_kreatif->nama_pemilik->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_nama_industri_kreatif");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->nama_industri_kreatif->FldCaption(), $pelaku_kreatif->nama_industri_kreatif->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->_email->FldCaption(), $pelaku_kreatif->_email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->password->FldCaption(), $pelaku_kreatif->password->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_deskripsi_industri_kreatif");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->deskripsi_industri_kreatif->FldCaption(), $pelaku_kreatif->deskripsi_industri_kreatif->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_alamat");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->alamat->FldCaption(), $pelaku_kreatif->alamat->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_no_telp");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->no_telp->FldCaption(), $pelaku_kreatif->no_telp->ReqErrMsg)) ?>");
			felm = this.GetElements("x" + infix + "_image");
			elm = this.GetElements("fn_x" + infix + "_image");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->image->FldCaption(), $pelaku_kreatif->image->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_sektor_industri_kreatif");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->sektor_industri_kreatif->FldCaption(), $pelaku_kreatif->sektor_industri_kreatif->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_order_rank");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->order_rank->FldCaption(), $pelaku_kreatif->order_rank->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_created_at");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->created_at->FldCaption(), $pelaku_kreatif->created_at->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_created_at");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($pelaku_kreatif->created_at->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_role");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $pelaku_kreatif->role->FldCaption(), $pelaku_kreatif->role->ReqErrMsg)) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fpelaku_kreatifedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpelaku_kreatifedit.ValidateRequired = true;
<?php } else { ?>
fpelaku_kreatifedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpelaku_kreatifedit.Lists["x_sektor_industri_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_sektor","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $pelaku_kreatif_edit->ShowPageHeader(); ?>
<?php
$pelaku_kreatif_edit->ShowMessage();
?>
<form name="fpelaku_kreatifedit" id="fpelaku_kreatifedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($pelaku_kreatif_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $pelaku_kreatif_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pelaku_kreatif">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($pelaku_kreatif->id->Visible) { // id ?>
	<div id="r_id" class="form-group">
		<label id="elh_pelaku_kreatif_id" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->id->CellAttributes() ?>>
<span id="el_pelaku_kreatif_id">
<span<?php echo $pelaku_kreatif->id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $pelaku_kreatif->id->EditValue ?></p></span>
</span>
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($pelaku_kreatif->id->CurrentValue) ?>">
<?php echo $pelaku_kreatif->id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->nama_pemilik->Visible) { // nama_pemilik ?>
	<div id="r_nama_pemilik" class="form-group">
		<label id="elh_pelaku_kreatif_nama_pemilik" for="x_nama_pemilik" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->nama_pemilik->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->nama_pemilik->CellAttributes() ?>>
<span id="el_pelaku_kreatif_nama_pemilik">
<input type="text" data-field="x_nama_pemilik" name="x_nama_pemilik" id="x_nama_pemilik" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->nama_pemilik->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->nama_pemilik->EditValue ?>"<?php echo $pelaku_kreatif->nama_pemilik->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->nama_pemilik->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->nama_industri_kreatif->Visible) { // nama_industri_kreatif ?>
	<div id="r_nama_industri_kreatif" class="form-group">
		<label id="elh_pelaku_kreatif_nama_industri_kreatif" for="x_nama_industri_kreatif" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->nama_industri_kreatif->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->nama_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_nama_industri_kreatif">
<input type="text" data-field="x_nama_industri_kreatif" name="x_nama_industri_kreatif" id="x_nama_industri_kreatif" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->nama_industri_kreatif->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->nama_industri_kreatif->EditValue ?>"<?php echo $pelaku_kreatif->nama_industri_kreatif->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->nama_industri_kreatif->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->_email->Visible) { // email ?>
	<div id="r__email" class="form-group">
		<label id="elh_pelaku_kreatif__email" for="x__email" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->_email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->_email->CellAttributes() ?>>
<span id="el_pelaku_kreatif__email">
<input type="text" data-field="x__email" name="x__email" id="x__email" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->_email->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->_email->EditValue ?>"<?php echo $pelaku_kreatif->_email->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->_email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->password->Visible) { // password ?>
	<div id="r_password" class="form-group">
		<label id="elh_pelaku_kreatif_password" for="x_password" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->password->CellAttributes() ?>>
<span id="el_pelaku_kreatif_password">
<input type="password" data-field="x_password" name="x_password" id="x_password" value="<?php echo $pelaku_kreatif->password->EditValue ?>" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->password->PlaceHolder) ?>"<?php echo $pelaku_kreatif->password->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->password->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->deskripsi_industri_kreatif->Visible) { // deskripsi_industri_kreatif ?>
	<div id="r_deskripsi_industri_kreatif" class="form-group">
		<label id="elh_pelaku_kreatif_deskripsi_industri_kreatif" for="x_deskripsi_industri_kreatif" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->deskripsi_industri_kreatif->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_deskripsi_industri_kreatif">
<textarea data-field="x_deskripsi_industri_kreatif" name="x_deskripsi_industri_kreatif" id="x_deskripsi_industri_kreatif" cols="70" rows="10" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->deskripsi_industri_kreatif->PlaceHolder) ?>"<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->EditAttributes() ?>><?php echo $pelaku_kreatif->deskripsi_industri_kreatif->EditValue ?></textarea>
</span>
<?php echo $pelaku_kreatif->deskripsi_industri_kreatif->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->alamat->Visible) { // alamat ?>
	<div id="r_alamat" class="form-group">
		<label id="elh_pelaku_kreatif_alamat" for="x_alamat" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->alamat->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->alamat->CellAttributes() ?>>
<span id="el_pelaku_kreatif_alamat">
<textarea data-field="x_alamat" name="x_alamat" id="x_alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->alamat->PlaceHolder) ?>"<?php echo $pelaku_kreatif->alamat->EditAttributes() ?>><?php echo $pelaku_kreatif->alamat->EditValue ?></textarea>
</span>
<?php echo $pelaku_kreatif->alamat->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->no_telp->Visible) { // no_telp ?>
	<div id="r_no_telp" class="form-group">
		<label id="elh_pelaku_kreatif_no_telp" for="x_no_telp" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->no_telp->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->no_telp->CellAttributes() ?>>
<span id="el_pelaku_kreatif_no_telp">
<input type="text" data-field="x_no_telp" name="x_no_telp" id="x_no_telp" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->no_telp->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->no_telp->EditValue ?>"<?php echo $pelaku_kreatif->no_telp->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->no_telp->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->image->Visible) { // image ?>
	<div id="r_image" class="form-group">
		<label id="elh_pelaku_kreatif_image" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->image->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->image->CellAttributes() ?>>
<span id="el_pelaku_kreatif_image">
<div id="fd_x_image">
<span title="<?php echo $pelaku_kreatif->image->FldTitle() ? $pelaku_kreatif->image->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($pelaku_kreatif->image->ReadOnly || $pelaku_kreatif->image->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-field="x_image" name="x_image" id="x_image">
</span>
<input type="hidden" name="fn_x_image" id= "fn_x_image" value="<?php echo $pelaku_kreatif->image->Upload->FileName ?>">
<?php if (@$_POST["fa_x_image"] == "0") { ?>
<input type="hidden" name="fa_x_image" id= "fa_x_image" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_image" id= "fa_x_image" value="1">
<?php } ?>
<input type="hidden" name="fs_x_image" id= "fs_x_image" value="255">
<input type="hidden" name="fx_x_image" id= "fx_x_image" value="<?php echo $pelaku_kreatif->image->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_image" id= "fm_x_image" value="<?php echo $pelaku_kreatif->image->UploadMaxFileSize ?>">
</div>
<table id="ft_x_image" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $pelaku_kreatif->image->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->website->Visible) { // website ?>
	<div id="r_website" class="form-group">
		<label id="elh_pelaku_kreatif_website" for="x_website" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->website->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->website->CellAttributes() ?>>
<span id="el_pelaku_kreatif_website">
<input type="text" data-field="x_website" name="x_website" id="x_website" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->website->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->website->EditValue ?>"<?php echo $pelaku_kreatif->website->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->website->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->sektor_industri_kreatif->Visible) { // sektor_industri_kreatif ?>
	<div id="r_sektor_industri_kreatif" class="form-group">
		<label id="elh_pelaku_kreatif_sektor_industri_kreatif" for="x_sektor_industri_kreatif" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->sektor_industri_kreatif->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->sektor_industri_kreatif->CellAttributes() ?>>
<span id="el_pelaku_kreatif_sektor_industri_kreatif">
<select data-field="x_sektor_industri_kreatif" id="x_sektor_industri_kreatif" name="x_sektor_industri_kreatif"<?php echo $pelaku_kreatif->sektor_industri_kreatif->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->sektor_industri_kreatif->EditValue)) {
	$arwrk = $pelaku_kreatif->sektor_industri_kreatif->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->sektor_industri_kreatif->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpelaku_kreatifedit.Lists["x_sektor_industri_kreatif"].Options = <?php echo (is_array($pelaku_kreatif->sektor_industri_kreatif->EditValue)) ? ew_ArrayToJson($pelaku_kreatif->sektor_industri_kreatif->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $pelaku_kreatif->sektor_industri_kreatif->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->order_rank->Visible) { // order_rank ?>
	<div id="r_order_rank" class="form-group">
		<label id="elh_pelaku_kreatif_order_rank" for="x_order_rank" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->order_rank->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->order_rank->CellAttributes() ?>>
<span id="el_pelaku_kreatif_order_rank">
<select data-field="x_order_rank" id="x_order_rank" name="x_order_rank"<?php echo $pelaku_kreatif->order_rank->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->order_rank->EditValue)) {
	$arwrk = $pelaku_kreatif->order_rank->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->order_rank->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $pelaku_kreatif->order_rank->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->created_at->Visible) { // created_at ?>
	<div id="r_created_at" class="form-group">
		<label id="elh_pelaku_kreatif_created_at" for="x_created_at" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->created_at->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->created_at->CellAttributes() ?>>
<span id="el_pelaku_kreatif_created_at">
<input type="text" data-field="x_created_at" name="x_created_at" id="x_created_at" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->created_at->PlaceHolder) ?>" value="<?php echo $pelaku_kreatif->created_at->EditValue ?>"<?php echo $pelaku_kreatif->created_at->EditAttributes() ?>>
<?php if (!$pelaku_kreatif->created_at->ReadOnly && !$pelaku_kreatif->created_at->Disabled && @$pelaku_kreatif->created_at->EditAttrs["readonly"] == "" && @$pelaku_kreatif->created_at->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fpelaku_kreatifedit", "x_created_at", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $pelaku_kreatif->created_at->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->role->Visible) { // role ?>
	<div id="r_role" class="form-group">
		<label id="elh_pelaku_kreatif_role" for="x_role" class="col-sm-2 control-label ewLabel"><?php echo $pelaku_kreatif->role->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->role->CellAttributes() ?>>
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn()) { // Non system admin ?>
<span id="el_pelaku_kreatif_role">
<p class="form-control-static"><?php echo $pelaku_kreatif->role->EditValue ?></p>
</span>
<?php } else { ?>
<span id="el_pelaku_kreatif_role">
<select data-field="x_role" id="x_role" name="x_role"<?php echo $pelaku_kreatif->role->EditAttributes() ?>>
<?php
if (is_array($pelaku_kreatif->role->EditValue)) {
	$arwrk = $pelaku_kreatif->role->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pelaku_kreatif->role->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php } ?>
<?php echo $pelaku_kreatif->role->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fpelaku_kreatifedit.Init();
</script>
<?php
$pelaku_kreatif_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pelaku_kreatif_edit->Page_Terminate();
?>
