<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
<channel>

	<title>KohanaJobs.com</title>
	<description>Work opportunities for Kohana PHP freelancers</description>
	<link>http://www.kohanajobs.com/</link>
	<language>en</language>
	<copyright><?php echo date('Y') ?> KohanaJobs.com</copyright>
	
	<?php foreach ($jobs as $job): ?>
		<item>
			<title><?php echo $job->title ?></title>
			<description>
				<![CDATA[
				
				<p><?php echo $job->description ?></p>
				
				<h2>Apply</h2>
				<p><?php echo $job->apply ?></p>
				
				]]>
			</description>
			<link><?php echo URL::site('job/' . $job->id, TRUE) ?></link>
			<guid><?php echo URL::site('job/' . $job->id, TRUE) ?></guid>
			<pubDate><?php echo date(DATE_RSS, $job->created) ?></pubDate>
		</item>
	<?php endforeach ?>

</channel>
</rss>