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

$produk_search = NULL; // Initialize page object first

class cproduk_search extends cproduk {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{D322CAA1-B5DF-4315-A18C-262A6180EE7B}";

	// Table name
	var $TableName = 'produk';

	// Page object name
	var $PageObjName = 'produk_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
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
						$sSrchStr = "produklist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->nama_produk); // nama_produk
		$this->BuildSearchUrl($sSrchUrl, $this->deskripsi); // deskripsi
		$this->BuildSearchUrl($sSrchUrl, $this->id_pelaku_kreatif); // id_pelaku_kreatif
		$this->BuildSearchUrl($sSrchUrl, $this->harga); // harga
		$this->BuildSearchUrl($sSrchUrl, $this->foto_produk); // foto_produk
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

		// nama_produk
		$this->nama_produk->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nama_produk"));
		$this->nama_produk->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nama_produk");

		// deskripsi
		$this->deskripsi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_deskripsi"));
		$this->deskripsi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_deskripsi");

		// id_pelaku_kreatif
		$this->id_pelaku_kreatif->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_pelaku_kreatif"));
		$this->id_pelaku_kreatif->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_pelaku_kreatif");

		// harga
		$this->harga->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_harga"));
		$this->harga->AdvancedSearch->SearchOperator = $objForm->GetValue("z_harga");

		// foto_produk
		$this->foto_produk->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_foto_produk"));
		$this->foto_produk->AdvancedSearch->SearchOperator = $objForm->GetValue("z_foto_produk");
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
				$this->foto_produk->LinkAttrs["data-rel"] = "produk_x_foto_produk";
				$this->foto_produk->LinkAttrs["class"] = "ewLightbox";
			}
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id
			$this->id->EditAttrs["class"] = "form-control";
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = ew_HtmlEncode($this->id->AdvancedSearch->SearchValue);
			$this->id->PlaceHolder = ew_RemoveHtml($this->id->FldCaption());

			// nama_produk
			$this->nama_produk->EditAttrs["class"] = "form-control";
			$this->nama_produk->EditCustomAttributes = "";
			$this->nama_produk->EditValue = ew_HtmlEncode($this->nama_produk->AdvancedSearch->SearchValue);
			$this->nama_produk->PlaceHolder = ew_RemoveHtml($this->nama_produk->FldCaption());

			// deskripsi
			$this->deskripsi->EditAttrs["class"] = "form-control";
			$this->deskripsi->EditCustomAttributes = "";
			$this->deskripsi->EditValue = ew_HtmlEncode($this->deskripsi->AdvancedSearch->SearchValue);
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
			if (!$GLOBALS["produk"]->UserIDAllow("search")) $sWhereWrk = $GLOBALS["pelaku_kreatif"]->AddUserIDFilter($sWhereWrk);

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
			$this->harga->EditValue = ew_HtmlEncode($this->harga->AdvancedSearch->SearchValue);
			$this->harga->PlaceHolder = ew_RemoveHtml($this->harga->FldCaption());

			// foto_produk
			$this->foto_produk->EditAttrs["class"] = "form-control";
			$this->foto_produk->EditCustomAttributes = "";
			$this->foto_produk->EditValue = ew_HtmlEncode($this->foto_produk->AdvancedSearch->SearchValue);
			$this->foto_produk->PlaceHolder = ew_RemoveHtml($this->foto_produk->FldCaption());
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
		if (!ew_CheckInteger($this->nama_produk->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->nama_produk->FldErrMsg());
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
		$this->nama_produk->AdvancedSearch->Load();
		$this->deskripsi->AdvancedSearch->Load();
		$this->id_pelaku_kreatif->AdvancedSearch->Load();
		$this->harga->AdvancedSearch->Load();
		$this->foto_produk->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "produklist.php", "", $this->TableVar, TRUE);
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
if (!isset($produk_search)) $produk_search = new cproduk_search();

// Page init
$produk_search->Page_Init();

// Page main
$produk_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$produk_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var produk_search = new ew_Page("produk_search");
produk_search.PageID = "search"; // Page ID
var EW_PAGE_ID = produk_search.PageID; // For backward compatibility

// Form object
var fproduksearch = new ew_Form("fproduksearch");

// Form_CustomValidate event
fproduksearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fproduksearch.ValidateRequired = true;
<?php } else { ?>
fproduksearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fproduksearch.Lists["x_id_pelaku_kreatif"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama_pemilik","x_nama_industri_kreatif","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

fproduksearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_id");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($produk->id->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_nama_produk");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($produk->nama_produk->FldErrMsg()) ?>");

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
<?php if (!$produk_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $produk_search->ShowPageHeader(); ?>
<?php
$produk_search->ShowMessage();
?>
<form name="fproduksearch" id="fproduksearch" class="form-horizontal ewForm ewSearchForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($produk_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $produk_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="produk">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($produk_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($produk->id->Visible) { // id ?>
	<div id="r_id" class="form-group">
		<label for="x_id" class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_id"><?php echo $produk->id->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id" id="z_id" value="="></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->id->CellAttributes() ?>>
			<span id="el_produk_id">
<input type="text" data-field="x_id" name="x_id" id="x_id" size="30" placeholder="<?php echo ew_HtmlEncode($produk->id->PlaceHolder) ?>" value="<?php echo $produk->id->EditValue ?>"<?php echo $produk->id->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($produk->nama_produk->Visible) { // nama_produk ?>
	<div id="r_nama_produk" class="form-group">
		<label for="x_nama_produk" class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_nama_produk"><?php echo $produk->nama_produk->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_nama_produk" id="z_nama_produk" value="="></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->nama_produk->CellAttributes() ?>>
			<span id="el_produk_nama_produk">
<input type="text" data-field="x_nama_produk" name="x_nama_produk" id="x_nama_produk" size="30" placeholder="<?php echo ew_HtmlEncode($produk->nama_produk->PlaceHolder) ?>" value="<?php echo $produk->nama_produk->EditValue ?>"<?php echo $produk->nama_produk->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($produk->deskripsi->Visible) { // deskripsi ?>
	<div id="r_deskripsi" class="form-group">
		<label for="x_deskripsi" class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_deskripsi"><?php echo $produk->deskripsi->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_deskripsi" id="z_deskripsi" value="LIKE"></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->deskripsi->CellAttributes() ?>>
			<span id="el_produk_deskripsi">
<input type="text" data-field="x_deskripsi" name="x_deskripsi" id="x_deskripsi" size="35" placeholder="<?php echo ew_HtmlEncode($produk->deskripsi->PlaceHolder) ?>" value="<?php echo $produk->deskripsi->EditValue ?>"<?php echo $produk->deskripsi->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($produk->id_pelaku_kreatif->Visible) { // id_pelaku_kreatif ?>
	<div id="r_id_pelaku_kreatif" class="form-group">
		<label for="x_id_pelaku_kreatif" class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_id_pelaku_kreatif"><?php echo $produk->id_pelaku_kreatif->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_pelaku_kreatif" id="z_id_pelaku_kreatif" value="="></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->id_pelaku_kreatif->CellAttributes() ?>>
			<span id="el_produk_id_pelaku_kreatif">
<select data-field="x_id_pelaku_kreatif" id="x_id_pelaku_kreatif" name="x_id_pelaku_kreatif"<?php echo $produk->id_pelaku_kreatif->EditAttributes() ?>>
<?php
if (is_array($produk->id_pelaku_kreatif->EditValue)) {
	$arwrk = $produk->id_pelaku_kreatif->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($produk->id_pelaku_kreatif->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fproduksearch.Lists["x_id_pelaku_kreatif"].Options = <?php echo (is_array($produk->id_pelaku_kreatif->EditValue)) ? ew_ArrayToJson($produk->id_pelaku_kreatif->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($produk->harga->Visible) { // harga ?>
	<div id="r_harga" class="form-group">
		<label for="x_harga" class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_harga"><?php echo $produk->harga->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_harga" id="z_harga" value="LIKE"></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->harga->CellAttributes() ?>>
			<span id="el_produk_harga">
<input type="text" data-field="x_harga" name="x_harga" id="x_harga" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($produk->harga->PlaceHolder) ?>" value="<?php echo $produk->harga->EditValue ?>"<?php echo $produk->harga->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($produk->foto_produk->Visible) { // foto_produk ?>
	<div id="r_foto_produk" class="form-group">
		<label class="<?php echo $produk_search->SearchLabelClass ?>"><span id="elh_produk_foto_produk"><?php echo $produk->foto_produk->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_foto_produk" id="z_foto_produk" value="LIKE"></p>
		</label>
		<div class="<?php echo $produk_search->SearchRightColumnClass ?>"><div<?php echo $produk->foto_produk->CellAttributes() ?>>
			<span id="el_produk_foto_produk">
<input type="text" data-field="x_foto_produk" name="x_foto_produk" id="x_foto_produk" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($produk->foto_produk->PlaceHolder) ?>" value="<?php echo $produk->foto_produk->EditValue ?>"<?php echo $produk->foto_produk->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$produk_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fproduksearch.Init();
</script>
<?php
$produk_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$produk_search->Page_Terminate();
?>
