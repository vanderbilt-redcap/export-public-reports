<?php

namespace Vanderbilt\ExportPublicReports;

class ExportPublicReports extends \ExternalModules\AbstractExternalModule
{
	public function redcap_every_page_top() {
		if (!$this->isSurveyPage() || !isset($_GET['__report'])) {
			return;
		}

		?>
		<script>
			(() => {
				// Use setInterval() to wait until the containing div becomes visible on the page.
				const intervalId = setInterval(() => {
					const filterDiv = $('#report_table_filter')
					filterDiv.css({
						minWidth: '270px'
					})

					if (filterDiv.length === 0) {
						return
					}
				
					clearInterval(intervalId)

					const reportHash = new URLSearchParams(location.search).get('__report')

					// This is implemented as a link so that the URL is apparent to users who might want to include it in a script of some kind.
					const link = $('<a>', {
						href: <?=json_encode($this->getUrl('export-public-report.php', true))?> + '&reportHash=' + reportHash,
						target: 'about:blank',
						click(){
							const resultCount = parseInt($('.report-results-returned b').text().replaceAll(',', ''))
							if(resultCount > <?=$this->getReportSizeLimit()?>){
								alert('This report cannot be exported because it is larger than the export limit.')
								return false
							}
						}
					}).prependTo(filterDiv)

					$('<button>', {
						css: {
							marginRight: '10px',
						},
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
