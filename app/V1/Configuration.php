<?php

namespace App\V1;

class Configuration
{
    const ROOT_NAMESPACE = __NAMESPACE__;

    const CLOCK_BLOCK_KEYS = [
        'u&9zBJT4ztfLQM?Mp22r7ApPx$F3=jGkVMPGzhuxubrG^JawRe9haGpJrL^CaL8X',
        'FazYe6ghQ?n86GTDpFYYt!4AZM%%*Ye48!7w^MRqe?w#yjPLE-Lgq*Uy@jbq+7r*',
        'VMnJMTquv?vvux33G!?D6!jxeKDt?Yfqjx+*e7B6+C@7UU=qe3WsS*QPYxexHjkB',
        'm_dbQ7RH4bXynUr&9H%kVQcZp8gdHz3gqES-QN5nJH8p%yN@Gs@Vmz9Lv5*u6T+P',
        'Q-YfkLfu#8Dg2ZFAQH%ttbemgKudsx&#cWtr6uRW5&bNLNDRvmaD-mc!thtXGQ!9',
        'f^j&3cDj&J4$*-*yw3meFfC_Qc_r^4G+*td87YB58xF2JPUrQ!N68JDWvN*aC!AZ',
        'y?M3x_=tB5S!Dn!^yvKPEPdHs5$7!t^@rvUM2Yd%2gKbS$D&BVw5+LWzLCUJB+S?',
        'R^dANH-e^*?h6UK@uCR_a?dSX%aj7L%!^mM=#xzFY9E*=x3aF9uaLwvHBj4VHCVH',
    ];
    const CLOCK_BLOCK_RANGE = 30; // 30 minutes

    const HTTP_RESPONSE_STATUS_OK = 200;
    const HTTP_RESPONSE_STATUS_ERROR = 500;

    const DEFAULT_PAGINATION_ITEMS = 5;
    const DEFAULT_ITEMS_PER_PAGE = 10;
    const ALLOWED_ITEMS_PER_PAGE = [10, 20, 50, 100];

    const THROTTLE_REQUEST_MAX_ATTEMPTS = 60;
    const THROTTLE_REQUEST_DECAY_MINUTES = 1;

    const FETCH_QUERY = 0;
    const FETCH_PAGING_YES = 1;
    const FETCH_PAGING_NO = 2;

    const USER_SYSTEM_ID = 1;
    const USER_OWNER_ID = 2;
    const USER_SUPER_ADMINISTRATOR_ID = 3;
}
