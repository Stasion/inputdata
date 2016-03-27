<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "produk_kreatifinfo.php" ?>
<?php include_once "pelaku_kreatifinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$produk_kreatif_edit = NULL; // Initialize page object first

class cproduk_kreatif_edit extends cproduk_kreatif {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'produk_kreatif';

	// Page object name
	var $PageObjName = 'produk_kreatif_edit';

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

		// Table object (produk_kreatif)
		if (!isset($GLOBALS["produk_kreatif"]) || get_class($GLOBALS["produk_kreatif"]) == "cproduk_kreatif") {
			$GLOBALS["produk_kreatif"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["produk_kreatif"];
		}

		// Table object (pelaku_kreatif)
		if (!isset($GLOBALS['pelaku_kreatif'])) $GLOBALS['pelaku_kreatif'] = new cpelaku_kreatif();

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'produk_kreatif', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("produk_kreatiflist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("produk_kreatiflist.php"));
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
		global $EW_EXPORT, $produk_kreatif;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($produk_kreatif);
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
			$this->Page_Terminate("produk_kreatiflist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("produk_kreatiflist.php"); // No matching record, return to list
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
		$this->foto_produk->Upload->Index = $objForm->Index;
		$this->foto_produk->Upload->UploadFile();
		$this->foto_produk->CurrentValue = $this->foto_produk->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->nama_produk->FldIsDetailKey) {
			$this->nama_produk->setFormValue($objForm->GetValue("x_nama_produk"));
		}
		if (!$this->deskripsi->FldIsDetailKey) {
			$this->deskripsi->setFormValue($objForm->GetValue("x_deskripsi"));
		}
		if (!$this->id_pelaku_kreatif->FldIsDetailKey) {
			$this->id_pelaku_kreatif->setFormValue($objForm->GetValue("x_id_pelaku_kreatif"));
		}
		if (!$this->harga->FldIsDetailKey) {
			$this->harga->setFormValue($objForm->GetValue("x_harga"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
		$this->nama_produk->CurrentValue = $this->nama_produk->FormValue;
		$this->deskripsi->CurrentValue = $this->deskripsi->FormValue;
		$this->id_pelaku_kreatif->CurrentValue = $this->id_pelaku_kreatif->FormValue;
		$this->harga->CurrentValue = $this->harga->FormValue;
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
		$this->nama_produk->setDbValue($rs->fields('nama_produk'));
		$this->deskripsi->setDbValue($rs->fields('deskripsi'));
		$this->id_pelaku_kreatif->setDbValue($rs->fields('id_pelaku_kreatif'));
		$this->harga->setDbValue($rs->fields('harga'));
		$this->foto_produk->Upload->DbValue = $rs->fields('foto_produk');
		$this->foto_produk->CurrentValue = $this->foto_produk->Upload->DbValue;
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->nama_produk->DbValue = $row['nama_produk'];
		$this->deskripsi->DbValue = $row['deskripsi'];
		$this->id_pelaku_kreatif->DbValue = $row['id_pelaku_kreatif'];
		$this->harga->DbValue = $row['harga'];
		$this->foto_produk->Upload->DbValue = $row['foto_produk'];
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
		// nama_produk
		// deskripsi
		// id_pelaku_kreatif
		// harga
		// foto_produk

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// nama_produk
			$this->nama_produk->ViewValue = $this->nama_produk->CurrentValue;
			$this->nama_produk->ViewCustomAttributes = "";

			// deskripsi
			$this->deskripsi->ViewValue = $this->deskripsi->CurrentValue;
			$this->deskripsi->ViewCustomAttributes = "";

			// id_pelaku_kreatif
			$this->id_pelaku_kreatif->ViewValue = $this->id_pelaku_kreatif->CurrentValue;
			$this->id_pelaku_kreatif->ViewCustomAttributes = "";

			// harga
			$this->harga->ViewValue = $this->harga->CurrentValue;
			$this->harga->ViewCustomAttributes = "";

			// foto_produk
			if (!ew_Empty($this->foto_produk->Upload->DbValue)) {
				$this->foto_produk->ImageAlt = $this->foto_produk->FldAlt();
				$this->foto_produk->ViewValue = ew_UploadPathEx(FALSE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue;
				if ($this->CustomExport == "pdf" || $this->CustomExport == "email") {
					$this->foto_produk->ViewValue = ew_UploadPathEx(TRUE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue;
				}
			} else {
				$this->foto_produk->ViewValue = "";
			}
			$this->foto_produk->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// nama_produk
			$this->nama_produk->LinkCustomAttributes = "";
			$this->nama_produk->HrefValue = "";
			$this->nama_produk->TooltipValue = "";

			// deskripsi
			$this->deskripsi->LinkCustomAttributes = "";
			$this->deskripsi->HrefValue = "";
			$this->deskripsi->TooltipValue = "";

			// id_pelaku_kreatif
			$this->id_pelaku_kreatif->LinkCustomAttributes = "";
			$this->id_pelaku_kreatif->HrefValue = "";
			$this->id_pelaku_kreatif->TooltipValue = "";

			// harga
			$this->harga->LinkCustomAttributes = "";
			$this->harga->HrefValue = "";
			$this->harga->TooltipValue = "";

			// foto_produk
			$this->foto_produk->LinkCustomAttributes = "";
			if (!ew_Empty($this->foto_produk->Upload->DbValue)) {
				$this->foto_produk->HrefValue = ew_UploadPathEx(FALSE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue; // Add prefix/suffix
				$this->foto_produk->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->foto_produk->HrefValue = ew_ConvertFullUrl($this->foto_produk->HrefValue);
			} else {
				$this->foto_produk->HrefValue = "";
			}
			$this->foto_produk->HrefValue2 = $this->foto_produk->UploadPath . $this->foto_produk->Upload->DbValue;
			$this->foto_produk->TooltipValue = "";
			if ($this->foto_produk->UseColorbox) {
				$this->foto_produk->LinkAttrs["title"] = $Language->Phrase("ViewImageGallery");
				$this->foto_produk->LinkAttrs["data-rel"] = "produk_kreatif_x_foto_produk";
				$this->foto_produk->LinkAttrs["class"] = "ewLightbox";
			}
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditAttrs["class"] = "form-control";
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// nama_produk
			$this->nama_produk->EditAttrs["class"] = "form-control";
			$this->nama_produk->EditCustomAttributes = "";
			$this->nama_produk->EditValue = ew_HtmlEncode($this->nama_produk->CurrentValue);
			$this->nama_produk->PlaceHolder = ew_RemoveHtml($this->nama_produk->FldCaption());

			// deskripsi
			$this->deskripsi->EditAttrs["class"] = "form-control";
			$this->deskripsi->EditCustomAttributes = "";
			$this->deskripsi->EditValue = ew_HtmlEncode($this->deskripsi->CurrentValue);
			$this->deskripsi->PlaceHolder = ew_RemoveHtml($this->deskripsi->FldCaption());

			// id_pelaku_kreatif
			// harga

			$this->harga->EditAttrs["class"] = "form-control";
			$this->harga->EditCustomAttributes = "";
			$this->harga->EditValue = ew_HtmlEncode($this->harga->CurrentValue);
			$this->harga->PlaceHolder = ew_RemoveHtml($this->harga->FldCaption());

			// foto_produk
			$this->foto_produk->EditAttrs["class"] = "form-control";
			$this->foto_produk->EditCustomAttributes = "";
			if (!ew_Empty($this->foto_produk->Upload->DbValue)) {
				$this->foto_produk->ImageAlt = $this->foto_produk->FldAlt();
				$this->foto_produk->EditValue = ew_UploadPathEx(FALSE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue;
				if ($this->CustomExport == "pdf" || $this->CustomExport == "email") {
					$this->foto_produk->EditValue = ew_UploadPathEx(TRUE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue;
				}
			} else {
				$this->foto_produk->EditValue = "";
			}
			if (!ew_Empty($this->foto_produk->CurrentValue))
				$this->foto_produk->Upload->FileName = $this->foto_produk->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->foto_produk);

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// nama_produk
			$this->nama_produk->HrefValue = "";

			// deskripsi
			$this->deskripsi->HrefValue = "";

			// id_pelaku_kreatif
			$this->id_pelaku_kreatif->HrefValue = "";

			// harga
			$this->harga->HrefValue = "";

			// foto_produk
			if (!ew_Empty($this->foto_produk->Upload->DbValue)) {
				$this->foto_produk->HrefValue = ew_UploadPathEx(FALSE, $this->foto_produk->UploadPath) . $this->foto_produk->Upload->DbValue; // Add prefix/suffix
				$this->foto_produk->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->foto_produk->HrefValue = ew_ConvertFullUrl($this->foto_produk->HrefValue);
			} else {
				$this->foto_produk->HrefValue = "";
			}
			$this->foto_produk->HrefValue2 = $this->foto_produk->UploadPath . $this->foto_produk->Upload->DbValue;
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
		if (!$this->nama_produk->FldIsDetailKey && !is_null($this->nama_produk->FormValue) && $this->nama_produk->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->nama_produk->FldCaption(), $this->nama_produk->ReqErrMsg));
		}
		if (!$this->deskripsi->FldIsDetailKey && !is_null($this->deskripsi->FormValue) && $this->deskripsi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->deskripsi->FldCaption(), $this->deskripsi->ReqErrMsg));
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

			// nama_produk
			$this->nama_produk->SetDbValueDef($rsnew, $this->nama_produk->CurrentValue, "", $this->nama_produk->ReadOnly);

			// deskripsi
			$this->deskripsi->SetDbValueDef($rsnew, $this->deskripsi->CurrentValue, "", $this->deskripsi->ReadOnly);

			// id_pelaku_kreatif
			$this->id_pelaku_kreatif->SetDbValueDef($rsnew, CurrentUserID(), 0);
			$rsnew['id_pelaku_kreatif'] = &$this->id_pelaku_kreatif->DbValue;

			// harga
			$this->harga->SetDbValueDef($rsnew, $this->harga->CurrentValue, NULL, $this->harga->ReadOnly);

			// foto_produk
			if (!($this->foto_produk->ReadOnly) && !$this->foto_produk->Upload->KeepFile) {
				$this->foto_produk->Upload->DbValue = $rsold['foto_produk']; // Get original value
				if ($this->foto_produk->Upload->FileName == "") {
					$rsnew['foto_produk'] = NULL;
				} else {
					$rsnew['foto_produk'] = $this->foto_produk->Upload->FileName;
				}
			}
			if (!$this->foto_produk->Upload->KeepFile) {
				if (!ew_Empty($this->foto_produk->Upload->Value)) {
					$rsnew['foto_produk'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->foto_produk->UploadPath), $rsnew['foto_produk']); // Get new file name
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
					if (!$this->foto_produk->Upload->KeepFile) {
						if (!ew_Empty($this->foto_produk->Upload->Value)) {
							$this->foto_produk->Upload->SaveToFile($this->foto_produk->UploadPath, $rsnew['foto_produk'], TRUE);
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

		// foto_produk
		ew_CleanUploadTempPath($this->foto_produk, $this->foto_produk->Upload->Index);
		return $EditRow;
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->id_pelaku_kreatif->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "produk_kreatiflist.php", "", $this->TableVar, TRUE);
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
if (!isset($produk_kreatif_edit)) $produk_kreatif_edit = new cproduk_kreatif_edit();

// Page init
$produk_kreatif_edit->Page_Init();

// Page main
$produk_kreatif_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$produk_kreatif_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var produk_kreatif_edit = new ew_Page("produk_kreatif_edit");
produk_kreatif_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = produk_kreatif_edit.PageID; // For backward compatibility

// Form object
var fproduk_kreatifedit = new ew_Form("fproduk_kreatifedit");

// Validate form
fproduk_kreatifedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_nama_produk");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk_kreatif->nama_produk->FldCaption(), $produk_kreatif->nama_produk->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_deskripsi");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk_kreatif->deskripsi->FldCaption(), $produk_kreatif->deskripsi->ReqErrMsg)) ?>");

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
fproduk_kreatifedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fproduk_kreatifedit.ValidateRequired = true;
<?php } else { ?>
fproduk_kreatifedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
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
<?php $produk_kreatif_edit->ShowPageHeader(); ?>
<?php
$produk_kreatif_edit->ShowMessage();
?>
<form name="fproduk_kreatifedit" id="fproduk_kreatifedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($produk_kreatif_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $produk_kreatif_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="produk_kreatif">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($produk_kreatif->id->Visible) { // id ?>
	<div id="r_id" class="form-group">
		<label id="elh_produk_kreatif_id" class="col-sm-2 control-label ewLabel"><?php echo $produk_kreatif->id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $produk_kreatif->id->CellAttributes() ?>>
<span id="el_produk_kreatif_id">
<span<?php echo $produk_kreatif->id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $produk_kreatif->id->EditValue ?></p></span>
</span>
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($produk_kreatif->id->CurrentValue) ?>">
<?php echo $produk_kreatif->id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk_kreatif->nama_produk->Visible) { // nama_produk ?>
	<div id="r_nama_produk" class="form-group">
		<label id="elh_produk_kreatif_nama_produk" for="x_nama_produk" class="col-sm-2 control-label ewLabel"><?php echo $produk_kreatif->nama_produk->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk_kreatif->nama_produk->CellAttributes() ?>>
<span id="el_produk_kreatif_nama_produk">
<input type="text" data-field="x_nama_produk" name="x_nama_produk" id="x_nama_produk" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($produk_kreatif->nama_produk->PlaceHolder) ?>" value="<?php echo $produk_kreatif->nama_produk->EditValue ?>"<?php echo $produk_kreatif->nama_produk->EditAttributes() ?>>
</span>
<?php echo $produk_kreatif->nama_produk->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk_kreatif->deskripsi->Visible) { // deskripsi ?>
	<div id="r_deskripsi" class="form-group">
		<label id="elh_produk_kreatif_deskripsi" for="x_deskripsi" class="col-sm-2 control-label ewLabel"><?php echo $produk_kreatif->deskripsi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk_kreatif->deskripsi->CellAttributes() ?>>
<span id="el_produk_kreatif_deskripsi">
<textarea data-field="x_deskripsi" name="x_deskripsi" id="x_deskripsi" cols="70" rows="10" placeholder="<?php echo ew_HtmlEncode($produk_kreatif->deskripsi->PlaceHolder) ?>"<?php echo $produk_kreatif->deskripsi->EditAttributes() ?>><?php echo $produk_kreatif->deskripsi->EditValue ?></textarea>
</span>
<?php echo $produk_kreatif->deskripsi->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk_kreatif->harga->Visible) { // harga ?>
	<div id="r_harga" class="form-group">
		<label id="elh_produk_kreatif_harga" for="x_harga" class="col-sm-2 control-label ewLabel"><?php echo $produk_kreatif->harga->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $produk_kreatif->harga->CellAttributes() ?>>
<span id="el_produk_kreatif_harga">
<input type="text" data-field="x_harga" name="x_harga" id="x_harga" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($produk_kreatif->harga->PlaceHolder) ?>" value="<?php echo $produk_kreatif->harga->EditValue ?>"<?php echo $produk_kreatif->harga->EditAttributes() ?>>
</span>
<?php echo $produk_kreatif->harga->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk_kreatif->foto_produk->Visible) { // foto_produk ?>
	<div id="r_foto_produk" class="form-group">
		<label id="elh_produk_kreatif_foto_produk" class="col-sm-2 control-label ewLabel"><?php echo $produk_kreatif->foto_produk->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $produk_kreatif->foto_produk->CellAttributes() ?>>
<span id="el_produk_kreatif_foto_produk">
<div id="fd_x_foto_produk">
<span title="<?php echo $produk_kreatif->foto_produk->FldTitle() ? $produk_kreatif->foto_produk->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($produk_kreatif->foto_produk->ReadOnly || $produk_kreatif->foto_produk->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-field="x_foto_produk" name="x_foto_produk" id="x_foto_produk">
</span>
<input type="hidden" name="fn_x_foto_produk" id= "fn_x_foto_produk" value="<?php echo $produk_kreatif->foto_produk->Upload->FileName ?>">
<?php if (@$_POST["fa_x_foto_produk"] == "0") { ?>
<input type="hidden" name="fa_x_foto_produk" id= "fa_x_foto_produk" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_foto_produk" id= "fa_x_foto_produk" value="1">
<?php } ?>
<input type="hidden" name="fs_x_foto_produk" id= "fs_x_foto_produk" value="255">
<input type="hidden" name="fx_x_foto_produk" id= "fx_x_foto_produk" value="<?php echo $produk_kreatif->foto_produk->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_foto_produk" id= "fm_x_foto_produk" value="<?php echo $produk_kreatif->foto_produk->UploadMaxFileSize ?>">
</div>
<table id="ft_x_foto_produk" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $produk_kreatif->foto_produk->CustomMsg ?></div></div>
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
fproduk_kreatifedit.Init();
</script>
<?php
$produk_kreatif_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$produk_kreatif_edit->Page_Terminate();
?>
