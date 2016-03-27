<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "produkinfo.php" ?>
<?php include_once "pelaku_kreatifinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$produk_add = NULL; // Initialize page object first

class cproduk_add extends cproduk {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'produk';

	// Page object name
	var $PageObjName = 'produk_add';

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

		// Table object (produk)
		if (!isset($GLOBALS["produk"]) || get_class($GLOBALS["produk"]) == "cproduk") {
			$GLOBALS["produk"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["produk"];
		}

		// Table object (pelaku_kreatif)
		if (!isset($GLOBALS['pelaku_kreatif'])) $GLOBALS['pelaku_kreatif'] = new cpelaku_kreatif();

		// User table object (pelaku_kreatif)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cpelaku_kreatif();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'produk', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("produklist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

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
		global $EW_EXPORT, $produk;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($produk);
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id"] != "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->setKey("id", $this->id->CurrentValue); // Set up key
			} else {
				$this->setKey("id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("produklist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "produkview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->foto_produk->Upload->Index = $objForm->Index;
		$this->foto_produk->Upload->UploadFile();
		$this->foto_produk->CurrentValue = $this->foto_produk->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->nama_produk->CurrentValue = NULL;
		$this->nama_produk->OldValue = $this->nama_produk->CurrentValue;
		$this->deskripsi->CurrentValue = NULL;
		$this->deskripsi->OldValue = $this->deskripsi->CurrentValue;
		$this->id_pelaku_kreatif->CurrentValue = NULL;
		$this->id_pelaku_kreatif->OldValue = $this->id_pelaku_kreatif->CurrentValue;
		$this->harga->CurrentValue = "-";
		$this->foto_produk->Upload->DbValue = NULL;
		$this->foto_produk->OldValue = $this->foto_produk->Upload->DbValue;
		$this->foto_produk->CurrentValue = NULL; // Clear file related field
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
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
		$this->LoadOldRecord();
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
			if (strval($this->id_pelaku_kreatif->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->id_pelaku_kreatif->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `nama_pemilik` AS `DispFld`, `nama_industri_kreatif` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `pelaku_kreatif`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_pelaku_kreatif, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nama_pemilik`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_pelaku_kreatif->ViewValue = $rswrk->fields('DispFld');
					$this->id_pelaku_kreatif->ViewValue .= ew_ValueSeparator(1,$this->id_pelaku_kreatif) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->id_pelaku_kreatif->ViewValue = $this->id_pelaku_kreatif->CurrentValue;
				}
			} else {
				$this->id_pelaku_kreatif->ViewValue = NULL;
			}
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
				$this->foto_produk->LinkAttrs["data-rel"] = "produk_x_foto_produk";
				$this->foto_produk->LinkAttrs["class"] = "ewLightbox";
			}
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

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
			$this->id_pelaku_kreatif->EditAttrs["class"] = "form-control";
			$this->id_pelaku_kreatif->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id`, `nama_pemilik` AS `DispFld`, `nama_industri_kreatif` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `pelaku_kreatif`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if (!$GLOBALS["produk"]->UserIDAllow("add")) $sWhereWrk = $GLOBALS["pelaku_kreatif"]->AddUserIDFilter($sWhereWrk);

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_pelaku_kreatif, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nama_pemilik`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->id_pelaku_kreatif->EditValue = $arwrk;

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->foto_produk);

			// Edit refer script
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
		if (!ew_CheckInteger($this->nama_produk->FormValue)) {
			ew_AddMessage($gsFormError, $this->nama_produk->FldErrMsg());
		}
		if (!$this->deskripsi->FldIsDetailKey && !is_null($this->deskripsi->FormValue) && $this->deskripsi->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->deskripsi->FldCaption(), $this->deskripsi->ReqErrMsg));
		}
		if (!$this->id_pelaku_kreatif->FldIsDetailKey && !is_null($this->id_pelaku_kreatif->FormValue) && $this->id_pelaku_kreatif->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->id_pelaku_kreatif->FldCaption(), $this->id_pelaku_kreatif->ReqErrMsg));
		}
		if (!$this->harga->FldIsDetailKey && !is_null($this->harga->FormValue) && $this->harga->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->harga->FldCaption(), $this->harga->ReqErrMsg));
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

		// nama_produk
		$this->nama_produk->SetDbValueDef($rsnew, $this->nama_produk->CurrentValue, "", FALSE);

		// deskripsi
		$this->deskripsi->SetDbValueDef($rsnew, $this->deskripsi->CurrentValue, "", FALSE);

		// id_pelaku_kreatif
		$this->id_pelaku_kreatif->SetDbValueDef($rsnew, $this->id_pelaku_kreatif->CurrentValue, 0, FALSE);

		// harga
		$this->harga->SetDbValueDef($rsnew, $this->harga->CurrentValue, NULL, strval($this->harga->CurrentValue) == "");

		// foto_produk
		if (!$this->foto_produk->Upload->KeepFile) {
			$this->foto_produk->Upload->DbValue = ""; // No need to delete old file
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

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
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
		}

		// foto_produk
		ew_CleanUploadTempPath($this->foto_produk, $this->foto_produk->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "produklist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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
if (!isset($produk_add)) $produk_add = new cproduk_add();

// Page init
$produk_add->Page_Init();

// Page main
$produk_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$produk_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var produk_add = new ew_Page("produk_add");
produk_add.PageID = "add"; // Page ID
var EW_PAGE_ID = produk_add.PageID; // For backward compatibility

// Form object
var fprodukadd = new ew_Form("fprodukadd");

// Validate form
fprodukadd.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk->nama_produk->FldCaption(), $produk->nama_produk->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_nama_produk");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($produk->nama_produk->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_deskripsi");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk->deskripsi->FldCaption(), $produk->deskripsi->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_id_pelaku_kreatif");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk->id_pelaku_kreatif->FldCaption(), $produk->id_pelaku_kreatif->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_harga");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $produk->harga->FldCaption(), $produk->harga->ReqErrMsg)) ?>");

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
fprodukadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprodukadd.ValidateRequired = true;
<?php } else { ?>
fprodukadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fprodukadd.Lists["x_id_pelaku_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_pemilik","x_nama_industri_kreatif","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $produk_add->ShowPageHeader(); ?>
<?php
$produk_add->ShowMessage();
?>
<form name="fprodukadd" id="fprodukadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($produk_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $produk_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="produk">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($produk->nama_produk->Visible) { // nama_produk ?>
	<div id="r_nama_produk" class="form-group">
		<label id="elh_produk_nama_produk" for="x_nama_produk" class="col-sm-2 control-label ewLabel"><?php echo $produk->nama_produk->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk->nama_produk->CellAttributes() ?>>
<span id="el_produk_nama_produk">
<input type="text" data-field="x_nama_produk" name="x_nama_produk" id="x_nama_produk" size="30" placeholder="<?php echo ew_HtmlEncode($produk->nama_produk->PlaceHolder) ?>" value="<?php echo $produk->nama_produk->EditValue ?>"<?php echo $produk->nama_produk->EditAttributes() ?>>
</span>
<?php echo $produk->nama_produk->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk->deskripsi->Visible) { // deskripsi ?>
	<div id="r_deskripsi" class="form-group">
		<label id="elh_produk_deskripsi" for="x_deskripsi" class="col-sm-2 control-label ewLabel"><?php echo $produk->deskripsi->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk->deskripsi->CellAttributes() ?>>
<span id="el_produk_deskripsi">
<textarea data-field="x_deskripsi" name="x_deskripsi" id="x_deskripsi" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($produk->deskripsi->PlaceHolder) ?>"<?php echo $produk->deskripsi->EditAttributes() ?>><?php echo $produk->deskripsi->EditValue ?></textarea>
</span>
<?php echo $produk->deskripsi->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk->id_pelaku_kreatif->Visible) { // id_pelaku_kreatif ?>
	<div id="r_id_pelaku_kreatif" class="form-group">
		<label id="elh_produk_id_pelaku_kreatif" for="x_id_pelaku_kreatif" class="col-sm-2 control-label ewLabel"><?php echo $produk->id_pelaku_kreatif->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk->id_pelaku_kreatif->CellAttributes() ?>>
<span id="el_produk_id_pelaku_kreatif">
<select data-field="x_id_pelaku_kreatif" id="x_id_pelaku_kreatif" name="x_id_pelaku_kreatif"<?php echo $produk->id_pelaku_kreatif->EditAttributes() ?>>
<?php
if (is_array($produk->id_pelaku_kreatif->EditValue)) {
	$arwrk = $produk->id_pelaku_kreatif->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($produk->id_pelaku_kreatif->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
<?php if ($arwrk[$rowcntwrk][2] <> "") { ?>
<?php echo ew_ValueSeparator(1,$produk->id_pelaku_kreatif) ?><?php echo $arwrk[$rowcntwrk][2] ?>
<?php } ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fprodukadd.Lists["x_id_pelaku_kreatif"].Options = <?php echo (is_array($produk->id_pelaku_kreatif->EditValue)) ? ew_ArrayToJson($produk->id_pelaku_kreatif->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $produk->id_pelaku_kreatif->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk->harga->Visible) { // harga ?>
	<div id="r_harga" class="form-group">
		<label id="elh_produk_harga" for="x_harga" class="col-sm-2 control-label ewLabel"><?php echo $produk->harga->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $produk->harga->CellAttributes() ?>>
<span id="el_produk_harga">
<input type="text" data-field="x_harga" name="x_harga" id="x_harga" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($produk->harga->PlaceHolder) ?>" value="<?php echo $produk->harga->EditValue ?>"<?php echo $produk->harga->EditAttributes() ?>>
</span>
<?php echo $produk->harga->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($produk->foto_produk->Visible) { // foto_produk ?>
	<div id="r_foto_produk" class="form-group">
		<label id="elh_produk_foto_produk" class="col-sm-2 control-label ewLabel"><?php echo $produk->foto_produk->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $produk->foto_produk->CellAttributes() ?>>
<span id="el_produk_foto_produk">
<div id="fd_x_foto_produk">
<span title="<?php echo $produk->foto_produk->FldTitle() ? $produk->foto_produk->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($produk->foto_produk->ReadOnly || $produk->foto_produk->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-field="x_foto_produk" name="x_foto_produk" id="x_foto_produk">
</span>
<input type="hidden" name="fn_x_foto_produk" id= "fn_x_foto_produk" value="<?php echo $produk->foto_produk->Upload->FileName ?>">
<input type="hidden" name="fa_x_foto_produk" id= "fa_x_foto_produk" value="0">
<input type="hidden" name="fs_x_foto_produk" id= "fs_x_foto_produk" value="255">
<input type="hidden" name="fx_x_foto_produk" id= "fx_x_foto_produk" value="<?php echo $produk->foto_produk->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_foto_produk" id= "fm_x_foto_produk" value="<?php echo $produk->foto_produk->UploadMaxFileSize ?>">
</div>
<table id="ft_x_foto_produk" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $produk->foto_produk->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fprodukadd.Init();
</script>
<?php
$produk_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$produk_add->Page_Terminate();
?>
