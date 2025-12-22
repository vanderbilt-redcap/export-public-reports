<?php

namespace Vanderbilt\ExportPublicReports;

class ExportPublicReports extends \ExternalModules\AbstractExternalModule
{
	public function redcap_every_page_top() {
		$normalizedURI = str_replace('surveys/index.php?', 'surveys/?', $_SERVER['REQUEST_URI']);
		if (!str_starts_with($normalizedURI, '/surveys/?__report=')) {
			return;
		}

		?>
		<script>
			(() => {
				// Use setInterval() to wait until the containing div becomes visible on the page.
				const intervalId = setInterval(() => {
					const filterDiv = $('.report_pagenum_div').first()
					if (filterDiv.length === 0) {
						return
					}
				
					clearInterval(intervalId)

					const reportHash = new URLSearchParams(location.search).get('__report')

					// This is implemented as a link so that the URL is apparent to users who might want to include it in a script of some kind.
					const link = $('<a>', {
						href: <?=json_encode($this->getUrl('export-public-report.php', true))?> + '&reportHash=' + reportHash,
						target: 'about:blank',
						css: {
							float: 'right',
							marginTop: '1px'
						},
						click(){
							const resultCount = parseInt(filterDiv.find('.float-start span').last().text().replaceAll(',', ''))

							if(resultCount > <?=$this->getReportSizeLimit()?>){
								alert('This report cannot be exported because it is larger than the export limit.')
								return false
							}
						}
					}).appendTo(filterDiv)

					$('<button>', {
						text: 'Export as CSV',
					}).appendTo(link)
				}, 50)
			})()
		</script>
		<?php
	}

	public function getReportSizeLimit() {
		return intval($this->getProjectSetting('report-size-limit')) ?: 100000;
	}
}
