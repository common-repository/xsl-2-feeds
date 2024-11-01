<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" exclude-result-prefixes="kml atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:kml="http://earth.google.com/kml/2.2" xmlns:georss="http://www.georss.org/georss" xmlns:atom="http://www.w3.org/2005/Atom">
	<xsl:output encoding="utf-8" method="xml" indent="yes" cdata-section-elements="summary"/>
	<xsl:strip-space elements="*"/>
	<!--
	The feedURL parameter must be set to a dspace browse URL with a minimum of rpp and XML parameters.  
	The base parameter must be set to the base URL of the dspace instance.
	Customize this template as needed.  
	-->
	<xsl:param name="feedURL" select="document('http://maps.google.com/maps/ms?msa=0&amp;msid=201979540595259073159.0000011366d3841dcf330&amp;output=kml')"/>
	<xsl:param name="base">http://dspace.mit.edu</xsl:param>
	<xsl:attribute-set name="hrefs">
		<xsl:attribute name="type">html</xsl:attribute>
	</xsl:attribute-set>
	<xsl:variable name="dt" select="//root"/>
	<xsl:template match="/">
		<feed xmlns="http://www.w3.org/2005/Atom">
			<title type="text">Rome - Korean Markets &amp; Restaurants</title>
			<subtitle type="html">Michael&apos;s favorite Korean Restaurants in Rome, Italy</subtitle>
			<id>http://trabaria.com/</id>
			<updated><xsl:value-of select="$dt"/></updated>
			<link rel="alternate" type="text/html" hreflang="en" href="http://maps.google.com/maps/ms?msa=0&amp;msid=201979540595259073159.0000011366d3841dcf330"/>
			<rights>For anyone passionate about Korean food.</rights>
			<xsl:apply-templates select="$feedURL//kml:Placemark"/>
		</feed>
	</xsl:template>
	<xsl:template match="*[local-name()='Placemark']">
		<xsl:call-template name="s"/>
	</xsl:template>
	<xsl:template name="s">
		<xsl:element name="entry" namespace="http://www.w3.org/2005/Atom" xml:space="preserve">
			<xsl:element name="title" namespace="http://www.w3.org/2005/Atom" xml:space="preserve"><xsl:value-of select="kml:name"/></xsl:element>
			<xsl:element name="updated" namespace="http://www.w3.org/2005/Atom" xml:space="preserve"><xsl:value-of select="$dt"/></xsl:element>
			<xsl:element name="id" namespace="http://www.w3.org/2005/Atom" xml:space="preserve">kml:<xsl:value-of select="kml:Point/kml:coordinates"/></xsl:element>
			<xsl:element name="content" namespace="http://www.w3.org/2005/Atom" xml:space="preserve" use-attribute-sets="hrefs">
				<xsl:value-of select="kml:description"/>
			</xsl:element>
			<xsl:element name="author" namespace="http://www.w3.org/2005/Atom">
				<xsl:element name="name" namespace="http://www.w3.org/2005/Atom">
					Michael Marus
				</xsl:element>
			</xsl:element>			
			<xsl:element name="georss:point"><xsl:value-of select="substring-after(substring-before(kml:Point/kml:coordinates,',0.000000'),',')"/><xsl:text> </xsl:text><xsl:value-of select="substring-before(kml:Point/kml:coordinates,',')"/></xsl:element>
		</xsl:element>
	</xsl:template>
	<xsl:template match="@*">
		<xsl:attribute name="{local-name()}"><xsl:value-of select="."/></xsl:attribute>
	</xsl:template>
</xsl:stylesheet>
