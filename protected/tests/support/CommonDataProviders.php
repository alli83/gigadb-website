<?php
 /**
 * Common PHPUnit DataProviders functions
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
trait CommonDataProviders
{
	/**
     * Provide test data for functional testing of file size display
     *
     * Requires database to be loaded with gigadb_test_data.pgdmp
     * as those are real example from test dataset
     *
     * Expectations are for Metric (decimal) display of file size
     *
     * @see https://en.wikipedia.org/wiki/Gigabyte
     * @see https://packagist.org/packages/gabrielelana/byte-units
     *
     * @return array[][]
     **/
    public function adminFileExamplesOfAppropriateMetricDisplayOfFileSize() {
        return [
            'millet.chr.version2.3.fa.gz: 109B' => ["109", "B"],
            'Millet.fa.glean.cds.v3.gz: 13000B' => ["12.70", "KiB"],
            'Millet.fa.glean.pep.v3.gz: 85000000B' => ["81.06", "MiB"],
            'Millet.fa.glean.v3.gff: 14000000B' => ["13.35", "MiB"],
            'Millet_scaffoldVersion2.3.fa.gz: 109000B' => ["106.45", "KiB"],
        ];
    }
}

?>