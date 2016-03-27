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

$register = NULL; // Initialize page object first

class cregister extends cpelaku_kreatif {

	// Page ID
	var $PageID = 'register';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Page object name
	var $PageObjName = 'register';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
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
		return TRUE;
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
		if (!isset($GLOBALS["pelaku_kreatif"])) $GLOBALS["pelaku_kreatif"] = new cpelaku_kreatif();

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'register', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

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

	//
	// Page main
	//
	function Page_Main() {
		global $conn, $Security, $Language, $gsFormError, $objForm;
		global $Breadcrumb;

		// Set up Breadcrumb
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("register", "RegisterPage", $url, "", "", TRUE);
		$bUserExists = FALSE;
		if (@$_POST["a_register"] <> "") {

			// Get action
			$this->CurrentAction = $_POST["a_register"];
			$this->LoadFormValues(); // Get form values

			// Validate form
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->setFailureMessage($gsFormError);
			}
		} else {
			$this->CurrentAction = "I"; // Display blank record
			$this->LoadDefaultValues(); // Load default values
		}
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "A": // Add

				// Check for duplicate User ID
				$sFilter = str_replace("%u", ew_AdjustSql($this->_email->CurrentValue), EW_USER_NAME_FILTER);

				// Set up filter (SQL WHERE clause) and get return SQL
				// SQL constructor in pelaku_kreatif class, pelaku_kreatifinfo.php

				$this->CurrentFilter = $sFilter;
				$sUserSql = $this->SQL();
				if ($rs = $conn->Execute($sUserSql)) {
					if (!$rs->EOF) {
						$bUserExists = TRUE;
						$this->RestoreFormValues(); // Restore form values
						$this->setFailureMessage($Language->Phrase("UserExists")); // Set user exist message
					}
					$rs->Close();
				}
				if (!$bUserExists) {
					$this->SendEmail = TRUE; // Send email on add success
					if ($this->AddRow()) { // Add record
						if ($this->getSuccessMessage() == "")
							$this->setSuccessMessage($Language->Phrase("RegisterSuccess")); // Register success

						// Auto login user
						if ($Security->ValidateUser($this->_email->CurrentValue, $this->password->FormValue, TRUE)) {

							// Nothing to do
						}
						$this->Page_Terminate("home.php"); // Return
					} else {
						$this->RestoreFormValues(); // Restore form values
					}
				}
		}

		// Render row
		$this->RowType = EW_ROWTYPE_ADD; // Render add
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->image->Upload->Index = $objForm->Index;
		$this->image->Upload->UploadFile();
		$this->image->CurrentValue = $this->image->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->nama_pemilik->CurrentValue = NULL;
		$this->nama_pemilik->OldValue = $this->nama_pemilik->CurrentValue;
		$this->nama_industri_kreatif->CurrentValue = NULL;
		$this->nama_industri_kreatif->OldValue = $this->nama_industri_kreatif->CurrentValue;
		$this->_email->CurrentValue = NULL;
		$this->_email->OldValue = $this->_email->CurrentValue;
		$this->password->CurrentValue = NULL;
		$this->password->OldValue = $this->password->CurrentValue;
		$this->deskripsi_industri_kreatif->CurrentValue = NULL;
		$this->deskripsi_industri_kreatif->OldValue = $this->deskripsi_industri_kreatif->CurrentValue;
		$this->alamat->CurrentValue = "-";
		$this->no_telp->CurrentValue = NULL;
		$this->no_telp->OldValue = $this->no_telp->CurrentValue;
		$this->image->Upload->DbValue = NULL;
		$this->image->OldValue = $this->image->Upload->DbValue;
		$this->website->CurrentValue = "-";
		$this->sektor_industri_kreatif->CurrentValue = NULL;
		$this->sektor_industri_kreatif->OldValue = $this->sektor_industri_kreatif->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
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
		$this->password->ConfirmValue = $objForm->GetValue("c_password");
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
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->nama_pemilik->CurrentValue = $this->nama_pemilik->FormValue;
		$this->nama_industri_kreatif->CurrentValue = $this->nama_industri_kreatif->FormValue;
		$this->_email->CurrentValue = $this->_email->FormValue;
		$this->password->CurrentValue = $this->password->FormValue;
		$this->deskripsi_industri_kreatif->CurrentValue = $this->deskripsi_industri_kreatif->FormValue;
		$this->alamat->CurrentValue = $this->alamat->FormValue;
		$this->no_telp->CurrentValue = $this->no_telp->FormValue;
		$this->website->CurrentValue = $this->website->FormValue;
		$this->sektor_industri_kreatif->CurrentValue = $this->sektor_industri_kreatif->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

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

			// Edit refer script
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
			ew_AddMessage($gsFormError, $Language->Phrase("EnterUserName"));
		}
		if (!$this->password->FldIsDetailKey && !is_null($this->password->FormValue) && $this->password->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterPassword"));
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// nama_pemilik
		$this->nama_pemilik->SetDbValueDef($rsnew, $this->nama_pemilik->CurrentValue, "", FALSE);

		// nama_industri_kreatif
		$this->nama_industri_kreatif->SetDbValueDef($rsnew, $this->nama_industri_kreatif->CurrentValue, "", FALSE);

		// email
		$this->_email->SetDbValueDef($rsnew, $this->_email->CurrentValue, "", FALSE);

		// password
		$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, "", FALSE);

		// deskripsi_industri_kreatif
		$this->deskripsi_industri_kreatif->SetDbValueDef($rsnew, $this->deskripsi_industri_kreatif->CurrentValue, "", FALSE);

		// alamat
		$this->alamat->SetDbValueDef($rsnew, $this->alamat->CurrentValue, NULL, FALSE);

		// no_telp
		$this->no_telp->SetDbValueDef($rsnew, $this->no_telp->CurrentValue, "", FALSE);

		// image
		if (!$this->image->Upload->KeepFile) {
			$this->image->Upload->DbValue = ""; // No need to delete old file
			if ($this->image->Upload->FileName == "") {
				$rsnew['image'] = NULL;
			} else {
				$rsnew['image'] = $this->image->Upload->FileName;
			}
		}

		// website
		$this->website->SetDbValueDef($rsnew, $this->website->CurrentValue, NULL, strval($this->website->CurrentValue) == "");

		// sektor_industri_kreatif
		$this->sektor_industri_kreatif->SetDbValueDef($rsnew, $this->sektor_industri_kreatif->CurrentValue, 0, FALSE);

		// id
		if (!$this->image->Upload->KeepFile) {
			if (!ew_Empty($this->image->Upload->Value)) {
				$rsnew['image'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->image->UploadPath), $rsnew['image']); // Get new file name
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
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
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->id->setDbValue($conn->Insert_ID());
			$rsnew['id'] = $this->id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);

			// Call User Registered event
			$this->User_Registered($rsnew);
		}

		// image
		ew_CleanUploadTempPath($this->image, $this->image->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
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
	// $type = ''|'success'|'failure'
	function Message_Showing(&$msg, $type) {

		// Example:
		//if ($type == 'success') $msg = "your success message";

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

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// User Registered event
	function User_Registered(&$rs) {

	  //echo "User_Registered";
	//var_dump($rs);
	//exit();

	resizeImage("uploads/".$rs['image']);
	}

	// User Activated event
	function User_Activated(&$rs) {

	  //echo "User_Activated";
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($register)) $register = new cregister();

// Page init
$register->Page_Init();

// Page main
$register->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$register->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var register = new ew_Page("register");
register.PageID = "register"; // Page ID
var EW_PAGE_ID = register.PageID; // For backward compatibility

// Form object
var fregister = new ew_Form("fregister");

// Validate form
fregister.Validate = function() {
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
				return this.OnError(elm, ewLanguage.Phrase("EnterUserName"));
			elm = this.GetElements("x" + infix + "_password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterPassword"));
			if (fobj.c_password.value != fobj.x_password.value)
				return this.OnError(fobj.c_password, ewLanguage.Phrase("MismatchPassword"));
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

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
	return true;
}

// Form_CustomValidate event
fregister.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fregister.ValidateRequired = true;
<?php } else { ?>
fregister.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fregister.Lists["x_sektor_industri_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_sektor","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $register->ShowPageHeader(); ?>
<?php
$register->ShowMessage();
?>
<form name="fregister" id="fregister" class="form-horizontal ewForm ewRegisterForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($register->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $register->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="pelaku_kreatif">
<input type="hidden" name="a_register" id="a_register" value="A">
<div>
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
<input type="password" data-field="x_password" name="x_password" id="x_password" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->password->PlaceHolder) ?>"<?php echo $pelaku_kreatif->password->EditAttributes() ?>>
</span>
<?php echo $pelaku_kreatif->password->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($pelaku_kreatif->password->Visible) { // password ?>
	<div id="r_c_password" class="form-group">
		<label id="elh_c_pelaku_kreatif_password" for="c_password" class="col-sm-2 control-label ewLabel"><?php echo $Language->Phrase("Confirm") ?>&nbsp;<?php echo $pelaku_kreatif->password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $pelaku_kreatif->password->CellAttributes() ?>>
<span id="el_c_pelaku_kreatif_password">
<input type="password" data-field="c_password" name="c_password" id="c_password" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($pelaku_kreatif->password->PlaceHolder) ?>"<?php echo $pelaku_kreatif->password->EditAttributes() ?>>
</span>
</div></div>
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
<input type="hidden" name="fa_x_image" id= "fa_x_image" value="0">
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
fregister.Lists["x_sektor_industri_kreatif"].Options = <?php echo (is_array($pelaku_kreatif->sektor_industri_kreatif->EditValue)) ? ew_ArrayToJson($pelaku_kreatif->sektor_industri_kreatif->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $pelaku_kreatif->sektor_industri_kreatif->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("RegisterBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fregister.Init();
</script>
<?php
$register->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$register->Page_Terminate();
?>
