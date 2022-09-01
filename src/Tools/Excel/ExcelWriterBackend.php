<?php

namespace LBF\Tools\Excel;

use Exception;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Backend actions to create an excel file.
 * 
 * use LBF\Tools\Excel\ExcelWriterBackend;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires PHP 8.0 or greater
 * 
 * @since   LRS 3.19.6
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class ExcelWriterBackend {

    /**
     * The required files, paths & data to be written in creating an excel file.
     * 
     * ## To be replaced
     * - NEW_FILE_NAME
     * - TO_BE_FILLED
     * - BUILD_RELS
     * - PLACEHOLDER_SHEET_NAMES
     * - PLACEHOLDER_XML
     * 
     * @var array   REQUIRED_FILES
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    const REQUIRED_FILES = [
        '_rels' => [
            '.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        ],
        'xl' => [
            '_rels' => [
                'workbook.xml.rels' => 'BUILD_RELS',
            ],
            'theme' => [
                'theme1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office Theme">
    <a:themeElements>
        <a:clrScheme name="Office">
            <a:dk1>
                <a:sysClr val="windowText" lastClr="000000" />
            </a:dk1>
            <a:lt1>
                <a:sysClr val="window" lastClr="FFFFFF" />
            </a:lt1>
            <a:dk2>
                <a:srgbClr val="1F497D" />
            </a:dk2>
            <a:lt2>
                <a:srgbClr val="EEECE1" />
            </a:lt2>
            <a:accent1>
                <a:srgbClr val="4F81BD" />
            </a:accent1>
            <a:accent2>
                <a:srgbClr val="C0504D" />
            </a:accent2>
            <a:accent3>
                <a:srgbClr val="9BBB59" />
            </a:accent3>
            <a:accent4>
                <a:srgbClr val="8064A2" />
            </a:accent4>
            <a:accent5>
                <a:srgbClr val="4BACC6" />
            </a:accent5>
            <a:accent6>
                <a:srgbClr val="F79646" />
            </a:accent6>
            <a:hlink>
                <a:srgbClr val="0000FF" />
            </a:hlink>
            <a:folHlink>
                <a:srgbClr val="800080" />
            </a:folHlink>
        </a:clrScheme>
        <a:fontScheme name="Office">
            <a:majorFont>
                <a:latin typeface="Cambria" />
                <a:ea typeface="" />
                <a:cs typeface="" />
                <a:font script="Jpan" typeface="ＭＳ Ｐゴシック" />
                <a:font script="Hang" typeface="맑은 고딕" />
                <a:font script="Hans" typeface="宋体" />
                <a:font script="Hant" typeface="新細明體" />
                <a:font script="Arab" typeface="Times New Roman" />
                <a:font script="Hebr" typeface="Times New Roman" />
                <a:font script="Thai" typeface="Tahoma" />
                <a:font script="Ethi" typeface="Nyala" />
                <a:font script="Beng" typeface="Vrinda" />
                <a:font script="Gujr" typeface="Shruti" />
                <a:font script="Khmr" typeface="MoolBoran" />
                <a:font script="Knda" typeface="Tunga" />
                <a:font script="Guru" typeface="Raavi" />
                <a:font script="Cans" typeface="Euphemia" />
                <a:font script="Cher" typeface="Plantagenet Cherokee" />
                <a:font script="Yiii" typeface="Microsoft Yi Baiti" />
                <a:font script="Tibt" typeface="Microsoft Himalaya" />
                <a:font script="Thaa" typeface="MV Boli" />
                <a:font script="Deva" typeface="Mangal" />
                <a:font script="Telu" typeface="Gautami" />
                <a:font script="Taml" typeface="Latha" />
                <a:font script="Syrc" typeface="Estrangelo Edessa" />
                <a:font script="Orya" typeface="Kalinga" />
                <a:font script="Mlym" typeface="Kartika" />
                <a:font script="Laoo" typeface="DokChampa" />
                <a:font script="Sinh" typeface="Iskoola Pota" />
                <a:font script="Mong" typeface="Mongolian Baiti" />
                <a:font script="Viet" typeface="Times New Roman" />
                <a:font script="Uigh" typeface="Microsoft Uighur" />
            </a:majorFont>
            <a:minorFont>
                <a:latin typeface="Calibri" />
                <a:ea typeface="" />
                <a:cs typeface="" />
                <a:font script="Jpan" typeface="ＭＳ Ｐゴシック" />
                <a:font script="Hang" typeface="맑은 고딕" />
                <a:font script="Hans" typeface="宋体" />
                <a:font script="Hant" typeface="新細明體" />
                <a:font script="Arab" typeface="Arial" />
                <a:font script="Hebr" typeface="Arial" />
                <a:font script="Thai" typeface="Tahoma" />
                <a:font script="Ethi" typeface="Nyala" />
                <a:font script="Beng" typeface="Vrinda" />
                <a:font script="Gujr" typeface="Shruti" />
                <a:font script="Khmr" typeface="DaunPenh" />
                <a:font script="Knda" typeface="Tunga" />
                <a:font script="Guru" typeface="Raavi" />
                <a:font script="Cans" typeface="Euphemia" />
                <a:font script="Cher" typeface="Plantagenet Cherokee" />
                <a:font script="Yiii" typeface="Microsoft Yi Baiti" />
                <a:font script="Tibt" typeface="Microsoft Himalaya" />
                <a:font script="Thaa" typeface="MV Boli" />
                <a:font script="Deva" typeface="Mangal" />
                <a:font script="Telu" typeface="Gautami" />
                <a:font script="Taml" typeface="Latha" />
                <a:font script="Syrc" typeface="Estrangelo Edessa" />
                <a:font script="Orya" typeface="Kalinga" />
                <a:font script="Mlym" typeface="Kartika" />
                <a:font script="Laoo" typeface="DokChampa" />
                <a:font script="Sinh" typeface="Iskoola Pota" />
                <a:font script="Mong" typeface="Mongolian Baiti" />
                <a:font script="Viet" typeface="Arial" />
                <a:font script="Uigh" typeface="Microsoft Uighur" />
            </a:minorFont>
        </a:fontScheme>
        <a:fmtScheme name="Office">
            <a:fillStyleLst>
                <a:solidFill>
                    <a:schemeClr val="phClr" />
                </a:solidFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="50000" />
                                <a:satMod val="300000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="35000">
                            <a:schemeClr val="phClr">
                                <a:tint val="37000" />
                                <a:satMod val="300000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:tint val="15000" />
                                <a:satMod val="350000" />
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:lin ang="16200000" scaled="1" />
                </a:gradFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:shade val="51000" />
                                <a:satMod val="130000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="80000">
                            <a:schemeClr val="phClr">
                                <a:shade val="93000" />
                                <a:satMod val="130000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:shade val="94000" />
                                <a:satMod val="135000" />
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:lin ang="16200000" scaled="0" />
                </a:gradFill>
            </a:fillStyleLst>
            <a:lnStyleLst>
                <a:ln w="9525" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr">
                            <a:shade val="95000" />
                            <a:satMod val="105000" />
                        </a:schemeClr>
                    </a:solidFill>
                    <a:prstDash val="solid" />
                </a:ln>
                <a:ln w="25400" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr" />
                    </a:solidFill>
                    <a:prstDash val="solid" />
                </a:ln>
                <a:ln w="38100" cap="flat" cmpd="sng" algn="ctr">
                    <a:solidFill>
                        <a:schemeClr val="phClr" />
                    </a:solidFill>
                    <a:prstDash val="solid" />
                </a:ln>
            </a:lnStyleLst>
            <a:effectStyleLst>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blurRad="40000" dist="20000" dir="5400000" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="38000" />
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                </a:effectStyle>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blurRad="40000" dist="23000" dir="5400000" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="35000" />
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                </a:effectStyle>
                <a:effectStyle>
                    <a:effectLst>
                        <a:outerShdw blurRad="40000" dist="23000" dir="5400000" rotWithShape="0">
                            <a:srgbClr val="000000">
                                <a:alpha val="35000" />
                            </a:srgbClr>
                        </a:outerShdw>
                    </a:effectLst>
                    <a:scene3d>
                        <a:camera prst="orthographicFront">
                            <a:rot lat="0" lon="0" rev="0" />
                        </a:camera>
                        <a:lightRig rig="threePt" dir="t">
                            <a:rot lat="0" lon="0" rev="1200000" />
                        </a:lightRig>
                    </a:scene3d>
                    <a:sp3d>
                        <a:bevelT w="63500" h="25400" />
                    </a:sp3d>
                </a:effectStyle>
            </a:effectStyleLst>
            <a:bgFillStyleLst>
                <a:solidFill>
                    <a:schemeClr val="phClr" />
                </a:solidFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="40000" />
                                <a:satMod val="350000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="40000">
                            <a:schemeClr val="phClr">
                                <a:tint val="45000" />
                                <a:shade val="99000" />
                                <a:satMod val="350000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:shade val="20000" />
                                <a:satMod val="255000" />
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:path path="circle">
                        <a:fillToRect l="50000" t="-80000" r="50000" b="180000" />
                    </a:path>
                </a:gradFill>
                <a:gradFill rotWithShape="1">
                    <a:gsLst>
                        <a:gs pos="0">
                            <a:schemeClr val="phClr">
                                <a:tint val="80000" />
                                <a:satMod val="300000" />
                            </a:schemeClr>
                        </a:gs>
                        <a:gs pos="100000">
                            <a:schemeClr val="phClr">
                                <a:shade val="30000" />
                                <a:satMod val="200000" />
                            </a:schemeClr>
                        </a:gs>
                    </a:gsLst>
                    <a:path path="circle">
                        <a:fillToRect l="50000" t="50000" r="50000" b="50000" />
                    </a:path>
                </a:gradFill>
            </a:bgFillStyleLst>
        </a:fmtScheme>
    </a:themeElements>
    <a:objectDefaults />
    <a:extraClrSchemeLst />
</a:theme>'
            ],
            'worksheets' => [
                'sheet1.xml' => 'TO_BE_FILLED',
            ],
            'styles.xml' => '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="1">
    <font>
        <sz val="11" />
        <color theme="1" />
        <name val="MS Sans Serif" />
        <family val="2" />
        <scheme val="minor" />
    </font>
    <font>
        <sz val="10" />
        <color rgb="FF000000" tint="0" />
        <name val="Arial" />
    </font>
    <font><b />
        <sz val="9" />
        <color rgb="FF000000" tint="0" />
        <name val="Arial" />
    </font>
    <font>
        <sz val="9" />
        <color rgb="FF000000" tint="0" />
        <name val="Arial" />
    </font>
    <font>
        <sz val="9" />
        <color rgb="FF000000" tint="0" />
        <name val="Arial" />
    </font>
    <font>
        <sz val="9" />
        <color rgb="FF000000" tint="0" />
        <name val="Arial" />
    </font>
</fonts>
<fills count="2">
    <fill>
        <patternFill patternType="none" />
    </fill>
    <fill>
        <patternFill patternType="gray125" />
    </fill>
    <fill>
        <patternFill patternType="none">
            <fgColor rgb="FF000000" />
            <bgColor rgb="FFFFFFFF" />
        </patternFill>
    </fill>
    <fill>
        <patternFill patternType="solid">
            <fgColor rgb="FFC0C0C0" />
            <bgColor rgb="FFC0C0C0" />
        </patternFill>
    </fill>
    <fill>
        <patternFill patternType="none">
            <fgColor rgb="FF000000" />
            <bgColor rgb="FFFFFFFF" />
        </patternFill>
    </fill>
    <fill>
        <patternFill patternType="none">
            <fgColor rgb="FF000000" />
            <bgColor rgb="FFFFFFFF" />
        </patternFill>
    </fill>
    <fill>
        <patternFill patternType="none">
            <fgColor rgb="FF000000" />
            <bgColor rgb="FFFFFFFF" />
        </patternFill>
    </fill>
</fills>
<borders count="1">
    <border>
        <left />
        <right />
        <top />
        <bottom />
        <diagonal />
    </border>
    <border>
        <left style="thin" />
        <right style="thin" />
        <top style="thin" />
        <bottom style="thin" />
    </border>
    <border>
        <left style="thin">
            <color rgb="FFC0C0C0" />
        </left>
        <right style="thin">
            <color rgb="FFC0C0C0" />
        </right>
        <top style="thin">
            <color rgb="FFC0C0C0" />
        </top>
        <bottom style="thin">
            <color rgb="FFC0C0C0" />
        </bottom>
    </border>
    <border>
        <left style="thin">
            <color rgb="FFC0C0C0" />
        </left>
        <right style="thin">
            <color rgb="FFC0C0C0" />
        </right>
        <top style="thin">
            <color rgb="FFC0C0C0" />
        </top>
        <bottom style="thin">
            <color rgb="FFC0C0C0" />
        </bottom>
    </border>
    <border>
        <left style="thin">
            <color rgb="FFC0C0C0" />
        </left>
        <right style="thin">
            <color rgb="FFC0C0C0" />
        </right>
        <top style="thin">
            <color rgb="FFC0C0C0" />
        </top>
        <bottom style="thin">
            <color rgb="FFC0C0C0" />
        </bottom>
    </border>
</borders>
<cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" />
</cellStyleXfs>
<cellXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" />
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyFill="1" applyProtection="1" applyAlignment="1" applyFont="1" xfId="0">
        <alignment wrapText="0" vertical="center" horizontal="left" />
        <protection locked="1" hidden="0" />
    </xf>
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" applyFill="1" applyProtection="1" applyBorder="1" applyAlignment="1" applyFont="1" xfId="0">
        <alignment wrapText="0" vertical="center" horizontal="center" />
        <protection locked="1" hidden="0" />
    </xf>
    <xf numFmtId="0" fontId="3" fillId="4" borderId="2" applyFill="1" applyProtection="1" applyBorder="1" applyAlignment="1" applyFont="1" xfId="0">
        <alignment wrapText="0" vertical="center" horizontal="center" />
        <protection locked="1" hidden="0" />
    </xf>
    <xf numFmtId="0" fontId="4" fillId="5" borderId="3" applyFill="1" applyProtection="1" applyBorder="1" applyAlignment="1" applyFont="1" xfId="0">
        <alignment wrapText="1" vertical="center" horizontal="general" />
        <protection locked="1" hidden="0" />
    </xf>
    <xf numFmtId="0" fontId="5" fillId="6" borderId="4" applyFill="1" applyProtection="1" applyBorder="1" applyAlignment="1" applyFont="1" xfId="0">
        <alignment wrapText="1" vertical="center" horizontal="right" />
        <protection locked="1" hidden="0" />
    </xf>
</cellXfs>
<cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0" />
</cellStyles>
<dxfs count="0" />
<tableStyles count="0" defaultTableStyle="TableStyleMedium9" defaultPivotStyle="PivotStyleLight16" />
</styleSheet>',
            'workbook.xml' => 'PLACEHOLDER_SHEET_NAMES',
        ],
        '[Content_Types].xml' => 'PLACEHOLDER_XML',
    ];


    /**
     * Get the Column letter indexed by number.
     * 
     * @var array   A1NOTATION
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    const A1NOTATION = [
        1   => 'A',
        2   => 'B',
        3   => 'C',
        4   => 'D',
        5   => 'E',
        6   => 'F',
        7   => 'G',
        8   => 'H',
        9   => 'I',
        10  => 'J',
        11  => 'K',
        12  => 'L',
        13  => 'M',
        14  => 'N',
        15  => 'O',
        16  => 'P',
        17  => 'Q',
        18  => 'R',
        19  => 'S',
        20  => 'T',
        21  => 'U',
        22  => 'V',
        23  => 'W',
        24  => 'X',
        25  => 'Y',
        26  => 'Z',
        27  => 'AA',
        28  => 'AB',
        29  => 'AC',
        30  => 'AD',
        31  => 'AE',
        32  => 'AF',
        33  => 'AG',
        34  => 'AH',
        35  => 'AI',
        36  => 'AJ',
        37  => 'AK',
        38  => 'AL',
        39  => 'AM',
        40  => 'AN',
        41  => 'AO',
        42  => 'AP',
        43  => 'AQ',
        44  => 'AR',
        45  => 'AS',
        46  => 'AT',
        47  => 'AU',
        48  => 'AV',
        49  => 'AW',
        50  => 'AX',
        51  => 'AY',
        52  => 'AZ',
        53  => 'BA',
        54  => 'BB',
        55  => 'BC',
        56  => 'BD',
        57  => 'BE',
        58  => 'BF',
        59  => 'BG',
        60  => 'BH',
        61  => 'BI',
        62  => 'BJ',
        63  => 'BK',
        64  => 'BL',
        65  => 'BM',
        66  => 'BN',
        67  => 'BO',
        68  => 'BP',
        69  => 'BQ',
        70  => 'BR',
        71  => 'BS',
        72  => 'BT',
        73  => 'BU',
        74  => 'BV',
        75  => 'BW',
        76  => 'BX',
        77  => 'BY',
        78  => 'BZ',
        79  => 'CA',
        80  => 'CB',
        81  => 'CC',
        82  => 'CD',
        83  => 'CE',
        84  => 'CF',
        85  => 'CG',
        86  => 'CH',
        87  => 'CI',
        88  => 'CJ',
        89  => 'CK',
        90  => 'CL',
        91  => 'CM',
        92  => 'CN',
        93  => 'CO',
        94  => 'CP',
        95  => 'CQ',
        96  => 'CR',
        97  => 'CS',
        98  => 'CT',
        99  => 'CU',
        100 => 'CV',
        101 => 'CW',
        102 => 'CX',
        103 => 'CY',
        104 => 'CZ',
    ];


    /**
     * The counter position per row
     * 
     * @var integer $row
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private $row = 1;

    /**
     * The counter position per column
     * 
     * @var integer $column
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private $column = 1;

    /**
     * The path where the excel file should be created.
     * 
     * @var string   $create_path
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    protected string $create_path;

    /**
     * Name of the file (without .xlsx) to be created.
     * 
     * @var string  $file_name
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected string $file_name;

    /**
     * The path to the excel file to be generated.
     * 
     * @var string   $file_path
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    protected string $file_path;

    /**
     * The data to be written to the excel file.
     * 
     * @var array   $data
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    protected array $data = [];

    /**
     * Whether or not the first row should be considered a 'header row'.
     * 
     * @var bool    $header_row     Default: false
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected bool $header_row = false;

    /**
     * Set whether or not to return some basic feedback to the user.
     * 
     * @var bool   $generate_basic_feedback   Default: false
     * 
     * @access  protected
     * @since   LRS 3.19.6 
     */

    protected bool $generate_basic_feedback = false;

    /**
     * The contents of the user basic feedback.
     * 
     * @var string  $basic_feedback
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected string $basic_feedback;

    /**
     * Set whether or not to return some basic feedback to the user.
     * 
     * @var bool   $generate_verbose_feedback    Default: false
     * 
     * @access  protected
     * @since   LRS 3.19.6 
     */

    protected bool $generate_verbose_feedback = false;

    /**
     * The contents of the user verbose feedback.
     * 
     * @var array   $verbose_feedback
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected array $verbose_feedback;

    /**
     * The start time, if verbose feedback is required.
     * 
     * @var float   $start_time
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected float $start_time;

    /**
     * The end time, if verbose feedback is required.
     * 
     * @var float   $end_time
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected float $end_time;

    /**
     * Indicated which sheet is currently selected / being interacted with.
     * 
     * @var integer $selected_sheet Default: 1
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected int $selected_sheet = 1;

    /**
     * Counter to indicate the number of sheets in the current document.
     * 
     * @var integer $number_of_sheets   Default: 1
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected int $number_of_sheets = 0;

    /**
     * Array of all the sheet names required, indexed by sheet number.
     * 
     * @var array   $sheet_names
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected array $sheet_names = [];


    /**
     * Constructor method, blocks direct invocation of this class. 
     * Designed to be extended to.
     * 
     * @access  public
     * @since   LRS 3.19.6
     */

    public function __construct() {
        throw new Exception( "You may not invoke this class directly." );
    }


    /**
     * Generate the files required for the excel file. Pack them into the zip and rename.
     * 
     * @param   array   $data   The data to be written
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected function generate_files( array $data ): void {
        if ( $data == self::REQUIRED_FILES ) {
            echo 'Data not properly formed. We cannot continue';
            die;
        }

        // Create the various files
        foreach ( $data as $key => $value ) {
            $this->create_files( $key, $value );
        }

        $folder_path = $this->create_path . $this->file_name;

        // Zip the file, create the excel
        $zip = new ZipArchive;
        $zip->open( $this->file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE );

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $folder_path ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ( $files as $file ) {
            if ( !$file->isDir() ) {
                $file_path = $file->getRealPath();
                $relative_path = substr( $file_path, strlen( $folder_path ) + 1 );
                $zip->addFile( $file_path, $relative_path );
            }
        }

        $zip->close();

        // Delete the files
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $folder_path, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach( $files as $file ) {
            if ( $file->isDir() ) {
                rmdir( $file->getRealPath() );
            } else {
                unlink( $file->getRealPath() );
            }
        }
        rmdir( $folder_path );
        
        if ( $this->generate_basic_feedback ) {
            $this->basic_feedback = "{$this->file_name}.xlsx successfully generated";
        }
        if ( $this->generate_verbose_feedback ) {
            $this->end_time = microtime( true );
            $total_rows = $total_cols = 0;
            foreach ( $this->data as $index => $name ) {
                $total_rows   += $this->get_max_rows( $this->data[$index] );
                $total_cols   += $this->get_max_columns( $this->data[$index] );
            }
            $this->verbose_feedback = [
                'file_path'    => $this->file_path,
                'rows_created' => $total_rows,
                'cols_created' => $total_cols,
                'sheets_created' => $this->number_of_sheets,
                'time_taken'   => ( $this->end_time - $this->start_time ),
            ];
        }
    }


    /**
     * Recurively create the files and folders required in building an excel sheet.
     * 
     * @param   string          $folder     The folder or sub folder path to be created
     * @param   array|string    $value      The sub folder structure (if array) or file contents (if string).
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private function create_files( string $folder, array|string $value ): void {
        $path = $this->create_path . $this->file_name . '/' . $folder;
        if ( is_array( $value ) ) {
            @mkdir( directory: $path, recursive: true );
            foreach ( $value as $key => $item ) {
                $this->create_files( $folder . '/' . $key, $item );
            }
        } else {
            $file = fopen( $path, 'w' );
            fwrite( $file, $value );
            fclose( $file );
        }
    }


    /**
     * Generate the xml file contents which is the content of the spreadsheet.
     * 
     * @param   integer $index  The index of the data being written
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected function worksheet_xml_creator( int $index ): string {
        $xml = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        $pos = self::A1NOTATION[$this->get_max_columns( $this->data[$index] )] . $this->get_max_rows( $this->data[$index] );
        $xml .= '<dimension ref="A1:' . $pos . '" />';
        $xml .= '<sheetViews>';
        if ( $index == 1 ) {
            $xml .= '<sheetView tabSelected="1" workbookViewId="0" rightToLeft="false">';
        } else {
            $xml .= '<sheetView workbookViewId="0" rightToLeft="false">';
        }
        $xml .= '<selection activeCell="A1" sqref="A1" />
    </sheetView>
</sheetViews>
<sheetFormatPr defaultRowHeight="15" />';
        $xml .= '<sheetData>';

        foreach ( $this->data[$index] as $row ) {
            $xml .= $this->set_row( $row );
        }
        $this->row = 1;

        $xml .= '</sheetData>
    <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3" />
</worksheet>';

        return $xml;
    }


    /**
     * Create the xml for a row of data
     * 
     * @param   array   $row    The data of the row
     * 
     * @return  string
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private function set_row( array $row ): string {
        $xml = '<row outlineLevel="0" r="' . $this->row . '">';

        foreach ( $row as $cell ) {
            $pos = self::A1NOTATION[$this->column] . $this->row;
            if ( is_integer( $cell ) ) {
                $format = $this->row == 1 && $this->header_row ? 2 : 5;
                $xml .= '<c r="' . $pos . '" s="' . $format . '">
    <v>' . $cell . '</v>
</c>';
            } else {
                $format = $this->row == 1 && $this->header_row ? 2 : 1;
                $xml .= '<c r="' . $pos . '" s="' . $format . '" t="inlineStr">
    <is>
        <t>' . $cell . '</t>
    </is>
</c>';
            }
            $this->column++;
        }
        $xml .= '</row>';

        $this->row++;
        $this->column = 1; // reset the column counter
        return $xml;
    }


    /**
     * Generate the xl/workbook.xml file contents.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected function build_workbook(): string {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" mc:Ignorable="x15" xmlns:x15="http://schemas.microsoft.com/office/spreadsheetml/2010/11/main">
<fileVersion appName="xl" lastEdited="4" lowestEdited="4" rupBuild="4505" />
<workbookPr defaultThemeVersion="124226" />
<bookViews>
    <workbookView xWindow="0" yWindow="0" windowWidth="23895" windowHeight="14535" />
</bookViews>
    <sheets>';

        foreach ( $this->sheet_names as $index => $name ) {
            $xml .= '<sheet name="' . $name . '" sheetId="' . $index . '" r:id="rId' . $index . '" />';
        }

        $xml .= '</sheets>
    <calcPr calcId="125725" fullCalcOnLoad="true" />
</workbook>';

        return $xml;
    }


    /**
     * Generate the xl/_rels/workbook.xml.rels file contents.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected function build_rels(): string {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $i = 0;
        foreach ( $this->sheet_names as $index => $name ) {
            $xml .= '<Relationship Id="rId' . $index . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $index . '.xml" />';
            $i++;
        }
        $xml .= '<Relationship Id="rId' . ( $i + 1 ) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml" />';
        $xml .= '<Relationship Id="rId' . ( $i + 2 ) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml" />';
        $xml .= '</Relationships>';
        return $xml;
    }


    /**
     * Generate the '[Content_Types].xml] file contents.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.19.6
     */

    protected function build_content_types(): string {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
            <Override PartName="/xl/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml" />
            <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" />
            <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml" />
            <Default Extension="wmf" ContentType="image/x-wmf" />
            <Default Extension="xml" ContentType="application/xml" />
            <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" />
            <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" />';

            foreach ( $this->sheet_names as $index => $name ) {
                $xml .= '<Override PartName="/xl/worksheets/sheet' . $index .'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" />';
            }
    
            $xml .= '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml" />
        </Types>';

        return $xml;
    }


    /**
     * Return the maximum number of rows required for the spreadsheet.
     * 
     * @param   array   $data   The data to be inserted.
     * 
     * @return  integer
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private function get_max_rows( array $data ): int {
        return count( $data );
    }


    /**
     * Return the maximum number of columns required for the spreadsheet.
     * 
     * @param   array   $data   The data to be inserted.
     * 
     * @return  integer
     * 
     * @access  private
     * @since   LRS 3.19.6
     */

    private function get_max_columns( array $data ): int {
        $max = 1;
        foreach ( $data as $line ) {
            if ( !is_array( $line ) ) {
                continue;
            }
            $cols = count( $line );
            if ( $cols > $max ) {
                $max = $cols;
            }
        }
        if ( !isset( self::A1NOTATION[$max] ) ) {
            throw new Exception( "Number of columns exceed the maximum permitted columns (104)" );
        }
        return $max;
    }

}