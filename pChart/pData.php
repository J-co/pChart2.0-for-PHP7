<?php
/*
pDraw - class to manipulate data arrays

Version     : 2.2.2-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/01/2018

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* Axis configuration */
define("AXIS_FORMAT_DEFAULT", 680001);
define("AXIS_FORMAT_TIME", 680002);
define("AXIS_FORMAT_DATE", 680003);
define("AXIS_FORMAT_METRIC", 680004);
define("AXIS_FORMAT_CURRENCY", 680005);
define("AXIS_FORMAT_TRAFFIC", 680006);
define("AXIS_FORMAT_CUSTOM", 680007);
/* Axis position */
define("AXIS_POSITION_LEFT", 681001);
define("AXIS_POSITION_RIGHT", 681002);
define("AXIS_POSITION_TOP", 681001);
define("AXIS_POSITION_BOTTOM", 681002);
/* Families of data points */
define("SERIE_SHAPE_FILLEDCIRCLE", 681011);
define("SERIE_SHAPE_FILLEDTRIANGLE", 681012);
define("SERIE_SHAPE_FILLEDSQUARE", 681013);
define("SERIE_SHAPE_FILLEDDIAMOND", 681017);
define("SERIE_SHAPE_CIRCLE", 681014);
define("SERIE_SHAPE_TRIANGLE", 681015);
define("SERIE_SHAPE_SQUARE", 681016);
define("SERIE_SHAPE_DIAMOND", 681018);
/* Axis position */
define("AXIS_X", 682001);
define("AXIS_Y", 682002);
/* Define value limits */
define("ABSOLUTE_MIN", -10000000000000);
define("ABSOLUTE_MAX", 10000000000000);
/* Replacement to the PHP NULL keyword */
define("VOID", 0.123456789);
/* Euro symbol for GD fonts */
define("EURO_SYMBOL", utf8_encode("&#8364;"));

/* pData class definition */
class pData
{
	var $Data;
	var $Palette = [
		"0" => ["R" => 188,"G" => 224,"B" => 46,"Alpha" => 100],
		"1" => ["R" => 224,"G" => 100,"B" => 46,"Alpha" => 100],
		"2" => ["R" => 224,"G" => 214,"B" => 46,"Alpha" => 100],
		"3" => ["R" => 46,"G" => 151,"B" => 224,"Alpha" => 100],
		"4" => ["R" => 176,"G" => 46,"B" => 224,"Alpha" => 100],
		"5" => ["R" => 224,"G" => 46,"B" => 117,"Alpha" => 100],
		"6" => ["R" => 92,"G" => 224,"B" => 46,	"Alpha" => 100],
		"7" => ["R" => 224,"G" => 176,"B" => 46,"Alpha" => 100]
	];
	/* Class creator */
	function __construct()
	{
		$this->Data = [
			"XAxisDisplay" => AXIS_FORMAT_DEFAULT,
			"XAxisFormat" => NULL,
			"XAxisName" => NULL,
			"XAxisUnit" => NULL,
			"Abscissa" => NULL,
			"AbsicssaPosition" => AXIS_POSITION_BOTTOM,
			"Axis" => [0 => [
					"Display" => AXIS_FORMAT_DEFAULT,
					"Position" => AXIS_POSITION_LEFT,
					"Identity" => AXIS_Y
				]
			]
		];
	}

	/* Add a single point or an array to the given serie */
	function addPoints($Values, $SerieName = "Serie1")
	{
		if (!isset($this->Data["Series"][$SerieName])){
			$this->initialise($SerieName);
		}
		
		if (is_array($Values)) {
			foreach($Values as $Key => $Value) {
				$this->Data["Series"][$SerieName]["Data"][] = $Value;
			}
		} else {
			$this->Data["Series"][$SerieName]["Data"][] = $Values;
		}
		
		if ($Values != VOID) {
			$StrippedData = $this->stripVOID($this->Data["Series"][$SerieName]["Data"]);
			if (empty($StrippedData)) {
				$this->Data["Series"][$SerieName]["Max"] = 0;
				$this->Data["Series"][$SerieName]["Min"] = 0;
			} else {
				$this->Data["Series"][$SerieName]["Max"] = max($StrippedData);
				$this->Data["Series"][$SerieName]["Min"] = min($StrippedData);
			}
		}
	}
	
	/* In case you add points to the a serie with the same name - pSplit */
	function clearPoints(string $SerieName = "Serie1")
	{
		$this->Data["Series"][$SerieName]["Data"] = [];
	}

	/* Strip VOID values */
	function stripVOID($Values)
	{
		if (!is_array($Values)) {
			return [];
		}

		$Result = [];
		foreach($Values as $Key => $Value) {
			($Value != VOID) AND $Result[] = $Value;
		}

		return $Result;
	}

	/* Return the number of values contained in a given serie */
	function getSerieCount(string $Serie)
	{
		return (isset($this->Data["Series"][$Serie]["Data"])) ? count($this->Data["Series"][$Serie]["Data"]) : 0;
	}

	/* Remove a serie from the pData object */
	function removeSerie(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			unset($this->Data["Series"][$Serie]);
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return a value from given serie & index */ # UNUSED
	function getValueAt(string $Serie, int $Index = 0)
	{
		return (isset($this->Data["Series"][$Serie]["Data"][$Index])) ? $this->Data["Series"][$Serie]["Data"][$Index] : NULL;
	}

	/* Return the values array */ # UNUSED
	function getValues(string $Serie)
	{
		return (isset($this->Data["Series"][$Serie]["Data"])) ? $this->Data["Series"][$Serie]["Data"] : NULL;
	}

	/* Reverse the values in the given serie */
	function reverseSerie(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie]["Data"])) {
			$this->Data["Series"][$Serie]["Data"] = array_reverse($this->Data["Series"][$Serie]["Data"]);
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the sum of the serie values */
	function getSum(string $Serie)
	{
		return (isset($this->Data["Series"][$Serie])) ? array_sum($this->Data["Series"][$Serie]["Data"]) : NULL;
	}

	/* Return the max value of a given serie */
	function getMax(string $Serie)
	{
		return (isset($this->Data["Series"][$Serie]["Max"])) ? $this->Data["Series"][$Serie]["Max"] : NULL;
	}

	/* Return the min value of a given serie */
	function getMin(string $Serie)
	{
		return (isset($this->Data["Series"][$Serie]["Min"])) ? $this->Data["Series"][$Serie]["Min"] : NULL;
	}

	/* Set the description of a given serie */
	function setSerieShape(string $Serie, int $Shape = SERIE_SHAPE_FILLEDCIRCLE)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Shape"] = $Shape;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set the description of a given serie */
	function setSerieDescription(string $Serie, string $Description = "My serie")
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Description"] = $Description;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set a serie as "drawable" while calling a rendering function */
	function setSerieDrawable(string $Serie, bool $Drawable = TRUE)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["isDrawable"] = $Drawable;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set the icon associated to a given serie */
	function setSeriePicture(string $Serie, string $Picture = NULL)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Picture"] = $Picture;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Set the name of the X Axis */
	function setXAxisName(string $Name)
	{
		$this->Data["XAxisName"] = $Name;
	}

	/* Set the display mode of the  X Axis */
	function setXAxisDisplay($Mode, $Format = NULL)
	{
		$this->Data["XAxisDisplay"] = $Mode;
		$this->Data["XAxisFormat"] = $Format;
	}

	/* Set the unit that will be displayed on the X axis */
	function setXAxisUnit($Unit)
	{
		$this->Data["XAxisUnit"] = $Unit;
	}

	/* Set the serie that will be used as abscissa */
	function setAbscissa(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Abscissa"] = $Serie;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	function setAbsicssaPosition(int $Position = AXIS_POSITION_BOTTOM)
	{
		$this->Data["AbsicssaPosition"] = $Position;
	}

	/* Set the name of the abscissa axis */
	function setAbscissaName(string $Name)
	{
		$this->Data["AbscissaName"] = $Name;
	}
	
	/* Return the abscissa margin */
	function getAbscissaMargin()
	{
		foreach($this->Data["Axis"] as $AxisID => $Values) {
			if ($Values["Identity"] == AXIS_X) {
				return ($Values["Margin"]);
			}
		}

		return 0;
	}


	/* Create a scatter group specifying X and Y data series */
	function setScatterSerie($SerieX, $SerieY, $ID = 0)
	{
		if (isset($this->Data["Series"][$SerieX]) && isset($this->Data["Series"][$SerieY])) {
			$this->initScatterSerie($ID);
			$this->Data["ScatterSeries"][$ID]["X"] = $SerieX;
			$this->Data["ScatterSeries"][$ID]["Y"] = $SerieY;
		}
	}

	/* Set the shape of a given scatter serie */
	function setScatterSerieShape(int $ID, int $Shape = SERIE_SHAPE_FILLEDCIRCLE)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Shape"] = $Shape;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Set the description of a given scatter serie */
	function setScatterSerieDescription(int $ID, string $Description = "My serie")
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Description"] = $Description;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Set the icon associated to a given scatter serie */
	function setScatterSeriePicture(int $ID, $Picture = NULL)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Picture"] = $Picture;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Set a scatter serie as "drawable" while calling a rendering function */
	function setScatterSerieDrawable(int $ID, bool $Drawable = TRUE)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["isDrawable"] = $Drawable;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Define if a scatter serie should be draw with ticks */
	function setScatterSerieTicks(int $ID, int $Width = 0)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Ticks"] = $Width;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Define if a scatter serie should be draw with a special weight */
	function setScatterSerieWeight(int $ID, int $Weight = 0)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Weight"] = $Weight;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Associate a color to a scatter serie */
	function setScatterSerieColor(int $ID, array $Format)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			$this->Data["ScatterSeries"][$ID]["Color"] = [
				"R" => isset($Format["R"]) ? $Format["R"] : 0,
				"G" => isset($Format["G"]) ? $Format["G"] : 0,
				"B" => isset($Format["B"]) ? $Format["B"] : 0,
				"Alpha" => isset($Format["Alpha"]) ? $Format["Alpha"] : 100
			];
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Compute the series limits for an individual and global point of view */
	function limits()
	{
		$GlobalMin = ABSOLUTE_MAX;
		$GlobalMax = ABSOLUTE_MIN;
		foreach($this->Data["Series"] as $Key => $Value) {
			if ($this->Data["Abscissa"] != $Key && $this->Data["Series"][$Key]["isDrawable"] == TRUE) {
				if ($GlobalMin > $this->Data["Series"][$Key]["Min"]) {
					$GlobalMin = $this->Data["Series"][$Key]["Min"];
				}

				if ($GlobalMax < $this->Data["Series"][$Key]["Max"]) {
					$GlobalMax = $this->Data["Series"][$Key]["Max"];
				}
			}
		}

		$this->Data["Min"] = $GlobalMin;
		$this->Data["Max"] = $GlobalMax;
		return [$GlobalMin,$GlobalMax];
	}

	/* Mark all series as drawable */
	function setAllDrawable()
	{
		foreach($this->Data["Series"] as $Key => $Value) {
			if ($this->Data["Abscissa"] != $Key) {
				$this->Data["Series"][$Key]["isDrawable"] = TRUE;
			}
		}
	}

	/* Return the average value of the given serie */
	function getSerieAverage(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
			return (array_sum($SerieData) / count($SerieData));
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the geometric mean of the given serie */
	function getGeometricMean(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
			$Seriesum = 1;
			foreach($SerieData as $Key => $Value) {
				$Seriesum = $Seriesum * $Value;
			}

			return (pow($Seriesum, 1 / count($SerieData)));
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the harmonic mean of the given serie */
	function getHarmonicMean(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
			$Seriesum = 0;
			foreach($SerieData as $Key => $Value) {
				$Seriesum = $Seriesum + 1 / $Value;
			}

			return (count($SerieData) / $Seriesum);
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the standard deviation of the given serie */
	function getStandardDeviation(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$Average = $this->getSerieAverage($Serie);
			$SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
			$DeviationSum = 0;
			foreach($SerieData as $Key => $Value) {
				$DeviationSum = $DeviationSum + ($Value - $Average) * ($Value - $Average);
			}

			return sqrt($DeviationSum / count($SerieData));
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the Coefficient of variation of the given serie */
	function getCoefficientOfVariation(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$Average = $this->getSerieAverage($Serie);
			$StandardDeviation = $this->getStandardDeviation($Serie);
			return ($StandardDeviation != 0) ? ($StandardDeviation / $Average) : NULL;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the median value of the given serie */
	function getSerieMedian(string $Serie)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$SerieData = $this->stripVOID($this->Data["Series"][$Serie]["Data"]);
			sort($SerieData);
			$SerieCenter = floor(count($SerieData) / 2);
			return (isset($SerieData[$SerieCenter])) ? $SerieData[$SerieCenter] : NULL;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Return the x th percentile of the given serie */
	function getSeriePercentile(string $Serie = "Serie1", int $Percentil = 95)
	{
		if (!isset($this->Data["Series"][$Serie]["Data"])) {
			throw pException::InvalidInput("Invalid serie name");
		}

		$Values = count($this->Data["Series"][$Serie]["Data"]) - 1;
		($Values < 0) AND $Values = 0;
		$PercentilID = floor(($Values / 100) * $Percentil + .5);
		$SortedValues = $this->Data["Series"][$Serie]["Data"];
		sort($SortedValues);
		return (is_numeric($SortedValues[$PercentilID])) ? $SortedValues[$PercentilID] : NULL;
	}

	/* Add random values to a given serie */
	function addRandomValues(string $SerieName = "Serie1", array $Options = [])
	{
		$Values = isset($Options["Values"]) ? $Options["Values"] : 20;
		$Min = isset($Options["Min"]) ? $Options["Min"] : 0;
		$Max = isset($Options["Max"]) ? $Options["Max"] : 100;
		$withFloat = isset($Options["withFloat"]) ? $Options["withFloat"] : FALSE;
		for ($i = 0; $i <= $Values; $i++) {
			$Value = ($withFloat) ? (rand($Min * 100, $Max * 100) / 100) : rand($Min, $Max);
			$this->addPoints($Value, $SerieName);
		}
	}

	/* Test if we have valid data */
	function containsData() # UNUSED
	{
		if (!isset($this->Data["Series"])) {
			return FALSE;
		}

		foreach($this->Data["Series"] as $Key => $Value) {
			if ($this->Data["Abscissa"] != $Key && $this->Data["Series"][$Key]["isDrawable"] == TRUE) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/* Set the display mode of an Axis */
	function setAxisDisplay(int $AxisID, int $Mode = AXIS_FORMAT_DEFAULT, string $Format = NULL)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Display"] = $Mode;
			if ($Format != NULL) {
				$this->Data["Axis"][$AxisID]["Format"] = $Format;
			}
		}
	}

	/* Set the position of an Axis */
	function setAxisPosition(int $AxisID, int $Position = AXIS_POSITION_LEFT)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Position"] = $Position;
		}
	}

	/* Associate an unit to an axis */
	function setAxisUnit(int $AxisID, string $Unit)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Unit"] = $Unit;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Associate a name to an axis */
	function setAxisName(int $AxisID, string $Name)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Name"] = $Name;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Associate a color to an axis */
	function setAxisColor(int $AxisID, array $Format)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Color"] = [
				"R" => isset($Format["R"]) ? $Format["R"] : 0,
				"G" => isset($Format["G"]) ? $Format["G"] : 0,
				"B" => isset($Format["B"]) ? $Format["B"] : 0,
				"Alpha" => isset($Format["Alpha"]) ? $Format["Alpha"] : 100
			];
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Design an axis as X or Y member */
	function setAxisXY(int $AxisID, $Identity = AXIS_Y)
	{
		if (isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Identity"] = $Identity;
		} else {
			throw pException::InvalidInput("Invalid serie ID");
		}
	}

	/* Associate one data serie with one axis */
	function setSerieOnAxis(string $Serie, int $AxisID)
	{
		$PreviousAxis = $this->Data["Series"][$Serie]["Axis"];
		/* Create missing axis */
		if (!isset($this->Data["Axis"][$AxisID])) {
			$this->Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT;
			$this->Data["Axis"][$AxisID]["Identity"] = AXIS_Y;
		}

		$this->Data["Series"][$Serie]["Axis"] = $AxisID;
		/* Cleanup unused axis */
		$Found = FALSE;
		foreach($this->Data["Series"] as $SerieName => $Values) {
			if ($Values["Axis"] == $PreviousAxis) {
				$Found = TRUE;
			}
		}

		if (!$Found) {
			unset($this->Data["Axis"][$PreviousAxis]);
		}
	}

	/* Define if a serie should be draw with ticks */
	function setSerieTicks(string $Serie, int $Width = 0)
	{
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Ticks"] = $Width;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Define if a serie should be draw with a special weight */
	function setSerieWeight(string $Serie, int $Weight = 0)
	{	
		if (isset($this->Data["Series"][$Serie])) {
			$this->Data["Series"][$Serie]["Weight"] = $Weight;
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}
	}

	/* Returns the palette of the given serie */
	function getSeriePalette(string $Serie)
	{
		if (!isset($this->Data["Series"][$Serie])) {
			throw pException::InvalidInput("Invalid serie name");
		} else {
			return [
				"R" => $this->Data["Series"][$Serie]["Color"]["R"],
				"G" => $this->Data["Series"][$Serie]["Color"]["G"],
				"B" => $this->Data["Series"][$Serie]["Color"]["B"],
				"Alpha" => $this->Data["Series"][$Serie]["Color"]["Alpha"]
			];
		}
		
	}

	/* Set the color of one serie */
	function setPalette(string $Serie, array $Format = [])
	{

		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

		if (isset($this->Data["Series"][$Serie])) {
			$OldR = $this->Data["Series"][$Serie]["Color"]["R"];
			$OldG = $this->Data["Series"][$Serie]["Color"]["G"];
			$OldB = $this->Data["Series"][$Serie]["Color"]["B"];
			$this->Data["Series"][$Serie]["Color"]["R"] = $R;
			$this->Data["Series"][$Serie]["Color"]["G"] = $G;
			$this->Data["Series"][$Serie]["Color"]["B"] = $B;
			$this->Data["Series"][$Serie]["Color"]["Alpha"] = $Alpha;
			/* Do reverse processing on the internal palette array */
			foreach($this->Palette as $Key => $Value) {
				if ($Value["R"] == $OldR && $Value["G"] == $OldG && $Value["B"] == $OldB) {
					$this->Palette[$Key]["R"] = $R;
					$this->Palette[$Key]["G"] = $G;
					$this->Palette[$Key]["B"] = $B;
					$this->Palette[$Key]["Alpha"] = $Alpha;
				}
			}
		} else {
			throw pException::InvalidInput("Invalid serie name");
		}

	}

	/* Load a palette file */
	function loadPalette($FileName, bool $Overwrite = FALSE)
	{
		if (!file_exists($FileName)) {
			throw pException::InvalidResourcePath("Palette not found");
		}

		$buffer = file_get_contents($FileName);
		if ($buffer === false) {
			throw pException::InvalidInput("Invalid palette");
		}

		if ($Overwrite) {
			$this->Palette = [];
		}

		$lines = explode(PHP_EOL, $buffer);
		$ID = 0;

		foreach($lines as $line){
			$pal = explode(",", $line);
			if (count($pal) == 4) {
				list($R, $G, $B, $Alpha) = $pal;
				$this->Palette[$ID] = ["R" => intval($R),"G" => intval($G),"B" => intval($B),"Alpha" => intval($Alpha)];
				$ID++;
			}
		}

		/* Apply changes to current series */
		$ID = 0;
		if (isset($this->Data["Series"])) {
			foreach($this->Data["Series"] as $Key => $Value) {
				$this->Data["Series"][$Key]["Color"] = (!isset($this->Palette[$ID])) ? ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 0] : $this->Palette[$ID];
				$ID++;
			}
		}

	}

	/* Initialize a given scatter serie */
	function initScatterSerie(int $ID)
	{
		if (isset($this->Data["ScatterSeries"][$ID])) {
			throw pException::InvalidInput("Invalid scatter serie ID");
		}

		$this->Data["ScatterSeries"][$ID] = [
			"Description" => "Scatter " . $ID,
			"isDrawable" => TRUE,
			"Picture" => NULL,
			"Ticks" => 0,
			"Weight" => 0,
			"Color" => (isset($this->Palette[$ID])) ? $this->Palette[$ID] : ["R" => rand(0, 255), "B" => rand(0, 255), "G" => rand(0, 255), "Alpha" => 100]
		];
	}

	/* Initialize a given serie */
	function initialise(string $Serie)
	{
		$ID = (isset($this->Data["Series"])) ? count($this->Data["Series"]) : 0;
		
		$this->Data["Series"][$Serie] = [
			"Description" => $Serie,
			"isDrawable" => TRUE,
			"Picture" => NULL,
			"Max" => NULL,
			"Min" => NULL,
			"Axis" => 0,
			"Ticks" => 0,
			"Weight" => 0,
			"Shape" => SERIE_SHAPE_FILLEDCIRCLE,
			"Color" => (isset($this->Palette[$ID])) ? $this->Palette[$ID] : ["R" => rand(0, 255), "B" => rand(0, 255), "G" => rand(0, 255), "Alpha" => 100]
		];
	}

	function normalize($NormalizationFactor = 100, $UnitChange = NULL, $Round = 1)
	{
		$Abscissa = $this->Data["Abscissa"];
		$SelectedSeries = [];
		$MaxVal = 0;
		foreach($this->Data["Axis"] as $AxisID => $Axis) {
			
			($UnitChange != NULL) AND $this->Data["Axis"][$AxisID]["Unit"] = $UnitChange;
		
			foreach($this->Data["Series"] as $SerieName => $Serie) {
				if ($Serie["Axis"] == $AxisID && $Serie["isDrawable"] == TRUE && $SerieName != $Abscissa) {
					$SelectedSeries[$SerieName] = $SerieName;
					if (count($Serie["Data"]) > $MaxVal) {
						$MaxVal = count($Serie["Data"]);
					}
				}
			}
		}

		for ($i = 0; $i <= $MaxVal - 1; $i++) {
			$Factor = 0;
			foreach($SelectedSeries as $Key => $SerieName) {
				$Value = $this->Data["Series"][$SerieName]["Data"][$i];
				($Value != VOID) AND $Factor = $Factor + abs($Value);
			}

			if ($Factor != 0) {
				$Factor = $NormalizationFactor / $Factor;
				foreach($SelectedSeries as $Key => $SerieName) {
					$Value = $this->Data["Series"][$SerieName]["Data"][$i];
					if ($Value != VOID && $Factor != $NormalizationFactor) {
						$this->Data["Series"][$SerieName]["Data"][$i] = round(abs($Value) * $Factor, $Round);
					} elseif ($Value == VOID || $Value == 0) {
						$this->Data["Series"][$SerieName]["Data"][$i] = VOID;
					} elseif ($Factor == $NormalizationFactor) {
						$this->Data["Series"][$SerieName]["Data"][$i] = $NormalizationFactor;
					}
				}
			}
		}

		foreach($SelectedSeries as $Key => $SerieName) {
			$data = $this->stripVOID($this->Data["Series"][$SerieName]["Data"]);
			$this->Data["Series"][$SerieName]["Max"] = max($data);
			$this->Data["Series"][$SerieName]["Min"] = min($data);
		}
	}

	/* Load data from a CSV (or similar) data source */
	function importFromCSV($FileName, array $Options = [])
	{
		$Delimiter = isset($Options["Delimiter"]) ? $Options["Delimiter"] : ",";
		$GotHeader = isset($Options["GotHeader"]) ? $Options["GotHeader"] : FALSE;
		$SkipColumns = isset($Options["SkipColumns"]) ? $Options["SkipColumns"] : [-1];
		$DefaultSerieName = isset($Options["DefaultSerieName"]) ? $Options["DefaultSerieName"] : "Serie";
		
		if (strlen($Delimiter) > 1){
			die("pChart: delimiter has to be a single char"); # No need to throw exception here
		}
		
		if (!file_exists($FileName)){
			die("pChart: could not find the CSV file"); # No need to throw exception here
		}
		
		$CSVContent = file($FileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$SerieNames = [];
		
		if ($GotHeader) {
			$line1 = explode($Delimiter, array_shift($CSVContent));
			foreach($line1 as $Key => $Name) {
				(!in_array($Key, $SkipColumns)) AND $SerieNames[$Key] = $Name;
			}
		}
			
		foreach ($CSVContent as $line){

			$line = str_replace([chr(10),chr(13)], ["",""], $line); 
			$Values = explode($Delimiter, $line);

			if (count($SerieNames) == 0) {
				foreach($Values as $Key => $Name) {
					(!in_array($Key, $SkipColumns)) AND $SerieNames[$Key] = $DefaultSerieName . $Key;
				}
			}

			foreach($Values as $Key => $Value) {
				(!in_array($Key, $SkipColumns)) AND $this->addPoints($Value, $SerieNames[$Key]);
			}
		
		}
	}
	
	/* Returns the number of drawable series */
	function countDrawableSeries()
	{
		$Results = 0;
		foreach($this->Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $this->Data["Abscissa"]) {
				$Results++;
			}
		}

		return $Results;
	}

	/* Create a dataset based on a formula */
	function createFunctionSerie(string $SerieName, string $Formula = "", array $Options = [])
	{
		
		if ($Formula == "") {
			throw pException::InvalidInput("Serie formula can not be empty");
		}
		
		$MinX = isset($Options["MinX"]) ? $Options["MinX"] : -10;
		$MaxX = isset($Options["MaxX"]) ? $Options["MaxX"] : 10;
		$XStep = isset($Options["XStep"]) ? $Options["XStep"] : 1;
		$AutoDescription = isset($Options["AutoDescription"]) ? $Options["AutoDescription"] : FALSE;
		$RecordAbscissa = isset($Options["RecordAbscissa"]) ? $Options["RecordAbscissa"] : FALSE;
		$AbscissaSerie = isset($Options["AbscissaSerie"]) ? $Options["AbscissaSerie"] : "Abscissa";
		
		$Result = [];
		$Abscissa = [];

		for ($i = $MinX; $i <= $MaxX; $i = $i + $XStep) {
							
			$Expression = "\$return = '!'.(" . str_replace("z", $i, $Formula) . ");";

			if (substr($Expression, -4, 4) == "/0);"){ # Division by zero in ..\pData.class.php(849) : eval()'d code on line 1
				$return = VOID;
			} else {
				if (eval($Expression) === FALSE) {
					$return = VOID;
				}
													
				$return = ($return == "!") ? VOID : substr($return, 1); # substr will remove the !
				
				if (in_array($return, ["NAN", "INF", "-INF"])){
					$return = VOID;
				}
			}
				
			$Abscissa[] = $i;
			$Result[] = $return;
		}

		$this->addPoints($Result, $SerieName);
		if ($AutoDescription) {
			$this->setSerieDescription($SerieName, $Formula);
		}

		if ($RecordAbscissa) {
			$this->addPoints($Abscissa, $AbscissaSerie);
		}

	}

	function negateValues(array $Series)
	{
		foreach($Series as $Key => $SerieName) {
			if (isset($this->Data["Series"][$SerieName])) {
				$Data = [];
				foreach($this->Data["Series"][$SerieName]["Data"] as $Key => $Value) {
					$Data[] = ($Value == VOID) ? VOID : - $Value;
				}

				$this->Data["Series"][$SerieName]["Data"] = $Data;
				$Data = $this->stripVOID($Data);
				$this->Data["Series"][$SerieName]["Max"] = max($Data);
				$this->Data["Series"][$SerieName]["Min"] = min($Data);
			}
		}
	}

	function scaleGetXSettings()
	{
		foreach($this->Data["Axis"] as $AxisID => $Settings) {
			if ($Settings["Identity"] == AXIS_X) {
				return [$Settings["Margin"],$Settings["Rows"]];
			}
		}
	}
		
	/* Return the data & configuration of the series */
	function getData()
	{
		return $this->Data;
	}

	/* Save a palette element */
	function savePalette(int $ID, array $Color)
	{
		$this->Palette[$ID] = $Color;
	}

	/* Return the palette of the series */
	function getPalette()
	{
		return $this->Palette;
	}

	/* Called by the scaling algorithm to save the config */
	function saveAxisConfig(array $Axis)
	{
		$this->Data["Axis"] = $Axis;
	}

	/* Save the Y Margin if set */
	function saveYMargin(int $Value)
	{
		$this->Data["YMargin"] = $Value;
	}

	/* Save extended configuration to the pData object */
	function saveExtendedData(string $Tag, $Values)
	{
		$this->Data["Extended"][$Tag] = $Values;
	}

	/* Called by the scaling algorithm to save the orientation of the scale */
	function saveOrientation(int $Orientation)
	{
		$this->Data["Orientation"] = $Orientation;
	}

}

?>