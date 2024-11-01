<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:trab="http://trabaria.com" xmlns="http://www.w3.org/2005/Atom" xmlns:dri="http://di.tamu.edu/DRI/1.0/" xmlns:i18n="http://apache.org/cocoon/i18n/2.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" xmlns:mets="http://www.loc.gov/METS/" xmlns:xlink="http://www.w3.org/TR/xlink/">
	<xsl:output encoding="utf-8" method="xml" indent="yes"/>
	<xsl:strip-space elements="*"/>
	<!--
	The feedURL parameter must be set to a dspace browse URL with a minimum of rpp and XML parameters.  
	The base parameter must be set to the base URL of the dspace instance.
	Customize this template as needed.  
	-->
	<xsl:param name="feedURL" select="document('http://dspace.mit.edu/handle/1721.1/3549/browse?order=ASC&amp;rpp=100&amp;sort_by=2&amp;etal=-1&amp;offset=0&amp;type=dateissued&amp;XML')"/>
	<xsl:param name="base">http://dspace.mit.edu</xsl:param>
	<xsl:attribute-set name="content">
		<xsl:attribute name="type">html</xsl:attribute>
	</xsl:attribute-set>
	<xsl:attribute-set name="meta">
		<xsl:attribute name="type">application/xml</xsl:attribute>
	</xsl:attribute-set>        
	<xsl:template match="/">
		<feed xmlns="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
			<title type="text">Center for Global Change Science</title>
			<subtitle type="html">Center for Global Change Science Document Collections</subtitle>
			<id>http://dspace.mit.edu/handle/1721.1/3549/browse</id>
                        <updated><xsl:value-of select="/root"/></updated>
			<link rel="alternate" type="text/html" hreflang="en" href="http://dspace.mit.edu/"/>
			<rights>All Items in DSpace@MIT are protected by original copyright, with all rights reserved, unless otherwise indicated.</rights>
			<xsl:apply-templates select="$feedURL//dri:referenceSet"/>
		</feed>
	</xsl:template>
	<xsl:template match="*[local-name()='reference']">
		<xsl:call-template name="s"/>
	</xsl:template>
	<xsl:template name="s">
		<xsl:variable name="itemurl" select="@url"/>
		<xsl:variable name="feeder" select="document(concat($base,$itemurl))"/>
		<xsl:variable name="url" select="$feeder//dim:field[@element='identifier'][@qualifier='uri']"/>
		<xsl:element name="entry" namespace="http://www.w3.org/2005/Atom" xml:space="preserve">
			<xsl:element name="title" namespace="http://www.w3.org/2005/Atom" xml:space="preserve"><xsl:value-of select="$feeder//dim:field[@element='title']"/></xsl:element>
			<xsl:element name="updated" namespace="http://www.w3.org/2005/Atom"><xsl:value-of select="$feeder//dim:field[@element='date'][@qualifier='accessioned']"/></xsl:element>
			<link href="{normalize-space($url)}" />
			<xsl:element name="id" namespace="http://www.w3.org/2005/Atom"><xsl:value-of select="$feeder//mets:METS/@ID"/></xsl:element>
			<xsl:element name="summary" use-attribute-sets="content" namespace="http://www.w3.org/2005/Atom">
				<xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
				<xsl:value-of select="$feeder//dim:field[@element='description'][@qualifier='abstract']" disable-output-escaping="no"/>
				<xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
			</xsl:element>
			<xsl:element name="author" namespace="http://www.w3.org/2005/Atom"><xsl:element name="name" namespace="http://www.w3.org/2005/Atom"><xsl:value-of select="$feeder//dim:field[@element='contributor'][@qualifier='author']"/></xsl:element></xsl:element>
                        <xsl:element name="content" use-attribute-sets="meta" namespace="http://www.w3.org/2005/Atom">
                        <xsl:for-each select="$feeder//mets:file"><xsl:element name="mets:file" namespace="http://www.loc.gov/METS/"><xsl:apply-templates select="@*|node()"/><xsl:value-of select="concat($base,mets:FLocat/@xlink:href)" disable-output-escaping="no"/></xsl:element></xsl:for-each>
			<xsl:for-each select="$feeder//dim:field[@qualifier!='abstract']"><xsl:element name="{@qualifier}"><xsl:apply-templates select="@*|node()"/></xsl:element></xsl:for-each>
                        </xsl:element>			
		</xsl:element>
	</xsl:template>
	<xsl:template match="@*">
		<xsl:attribute name="{local-name()}"><xsl:value-of select="."/></xsl:attribute>
	</xsl:template>	
</xsl:stylesheet>