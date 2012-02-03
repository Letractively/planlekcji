<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>
    <xsl:template match="timetable">
	<html>
	    <head>
		
		<meta charset="UTF-8"/>
		<title>Internetowy Plan Lekcji - klasa 
		    <xsl:value-of select="@class"/>
		</title>
		<style>
		    @import url('lib/css/style.css');
		    body{
		margin: 10px;
	    }
		</style>
	    </head>
	    <body>
		<table class="przed" align="center" style="font-size: 9pt; width: auto;">
		    <thead style="background: #ccccff;">
			<tr class="a_odd">
			    <td colspan="7" style="text-align: center">
				<p>
				    <span class="pltxt">
					<xsl:value-of select="@class"/>
				    </span>
				</p>
			    </td>
			</tr>
		    </thead>
		    <xsl:apply-templates/>
		</table>
	    </body>
	</html>
    </xsl:template>
    <xsl:template match="timetable/day[@name]">
	<tr>
	    <td>
		2d
	    </td>
	</tr>
    </xsl:template>
</xsl:stylesheet>
