<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'id' => 1,
                'name' => json_encode(['en' => 'Afghanistan', 'es' => 'Afganistán']),
                'iso2' => 'AF',
                'iso3' => 'AFG',
                'phone_code' => '+93'
            ],
            [
                'id' => 2,
                'name' => json_encode(['en' => 'Albania', 'es' => 'Albania']),
                'iso2' => 'AL',
                'iso3' => 'ALB',
                'phone_code' => '+355'
            ],
            [
                'id' => 3,
                'name' => json_encode(['en' => 'Algeria', 'es' => 'Argelia']),
                'iso2' => 'DZ',
                'iso3' => 'DZA',
                'phone_code' => '+213'
            ],
            [
                'id' => 4,
                'name' => json_encode(['en' => 'Andorra', 'es' => 'Andorra']),
                'iso2' => 'AD',
                'iso3' => 'AND',
                'phone_code' => '+376'
            ],
            [
                'id' => 5,
                'name' => json_encode(['en' => 'Angola', 'es' => 'Angola']),
                'iso2' => 'AO',
                'iso3' => 'AGO',
                'phone_code' => '+244'
            ],
            [
                'name' => json_encode(['en' => 'Antigua and Barbuda', 'es' => 'Antigua y Barbuda']),
                'iso2' => 'AG',
                'iso3' => 'ATG',
                'phone_code' => '+1268'
            ],
            [
                'name' => json_encode(['en' => 'Argentina', 'es' => 'Argentina']),
                'iso2' => 'AR',
                'iso3' => 'ARG',
                'phone_code' => '+54'
            ],
            [
                'name' => json_encode(['en' => 'Armenia', 'es' => 'Armenia']),
                'iso2' => 'AM',
                'iso3' => 'ARM',
                'phone_code' => '+374'
            ],
            [
                'name' => json_encode(['en' => 'Australia', 'es' => 'Australia']),
                'iso2' => 'AU',
                'iso3' => 'AUS',
                'phone_code' => '+61'
            ],
            [
                'name' => json_encode(['en' => 'Austria', 'es' => 'Austria']),
                'iso2' => 'AT',
                'iso3' => 'AUT',
                'phone_code' => '+43'
            ],
            [
                'name' => json_encode(['en' => 'Azerbaijan', 'es' => 'Azerbaiyán']),
                'iso2' => 'AZ',
                'iso3' => 'AZE',
                'phone_code' => '+994'
            ],
            [
                'name' => json_encode(['en' => 'Bahamas', 'es' => 'Bahamas']),
                'iso2' => 'BS',
                'iso3' => 'BHS',
                'phone_code' => '+1242'
            ],
            [
                'name' => json_encode(['en' => 'Bahrain', 'es' => 'Baréin']),
                'iso2' => 'BH',
                'iso3' => 'BHR',
                'phone_code' => '+973'
            ],
            [
                'name' => json_encode(['en' => 'Bangladesh', 'es' => 'Bangladés']),
                'iso2' => 'BD',
                'iso3' => 'BGD',
                'phone_code' => '+880'
            ],
            [
                'name' => json_encode(['en' => 'Barbados', 'es' => 'Barbados']),
                'iso2' => 'BB',
                'iso3' => 'BRB',
                'phone_code' => '+1246'
            ],
            [
                'name' => json_encode(['en' => 'Belarus', 'es' => 'Bielorrusia']),
                'iso2' => 'BY',
                'iso3' => 'BLR',
                'phone_code' => '+375'
            ],
            [
                'name' => json_encode(['en' => 'Belgium', 'es' => 'Bélgica']),
                'iso2' => 'BE',
                'iso3' => 'BEL',
                'phone_code' => '+32'
            ],
            [
                'name' => json_encode(['en' => 'Belize', 'es' => 'Belice']),
                'iso2' => 'BZ',
                'iso3' => 'BLZ',
                'phone_code' => '+501'
            ],
            [
                'name' => json_encode(['en' => 'Benin', 'es' => 'Benín']),
                'iso2' => 'BJ',
                'iso3' => 'BEN',
                'phone_code' => '+229'
            ],
            [
                'name' => json_encode(['en' => 'Bhutan', 'es' => 'Bután']),
                'iso2' => 'BT',
                'iso3' => 'BTN',
                'phone_code' => '+975'
            ],
            [
                'name' => json_encode(['en' => 'Bolivia', 'es' => 'Bolivia']),
                'iso2' => 'BO',
                'iso3' => 'BOL',
                'phone_code' => '+591'
            ],
            [
                'name' => json_encode(['en' => 'Bosnia and Herzegovina', 'es' => 'Bosnia y Herzegovina']),
                'iso2' => 'BA',
                'iso3' => 'BIH',
                'phone_code' => '+387'
            ],
            [
                'name' => json_encode(['en' => 'Botswana', 'es' => 'Botsuana']),
                'iso2' => 'BW',
                'iso3' => 'BWA',
                'phone_code' => '+267'
            ],
            [
                'name' => json_encode(['en' => 'Brazil', 'es' => 'Brasil']),
                'iso2' => 'BR',
                'iso3' => 'BRA',
                'phone_code' => '+55'
            ],
            [
                'name' => json_encode(['en' => 'Brunei', 'es' => 'Brunéi']),
                'iso2' => 'BN',
                'iso3' => 'BRN',
                'phone_code' => '+673'
            ],
            [
                'name' => json_encode(['en' => 'Bulgaria', 'es' => 'Bulgaria']),
                'iso2' => 'BG',
                'iso3' => 'BGR',
                'phone_code' => '+359'
            ],
            [
                'name' => json_encode(['en' => 'Burkina Faso', 'es' => 'Burkina Faso']),
                'iso2' => 'BF',
                'iso3' => 'BFA',
                'phone_code' => '+226'
            ],
            [
                'name' => json_encode(['en' => 'Burundi', 'es' => 'Burundi']),
                'iso2' => 'BI',
                'iso3' => 'BDI',
                'phone_code' => '+257'
            ],
            [
                'name' => json_encode(['en' => 'Cambodia', 'es' => 'Camboya']),
                'iso2' => 'KH',
                'iso3' => 'KHM',
                'phone_code' => '+855'
            ],
            [
                'name' => json_encode(['en' => 'Cameroon', 'es' => 'Camerún']),
                'iso2' => 'CM',
                'iso3' => 'CMR',
                'phone_code' => '+237'
            ],
            [
                'name' => json_encode(['en' => 'Canada', 'es' => 'Canadá']),
                'iso2' => 'CA',
                'iso3' => 'CAN',
                'phone_code' => '+1'
            ],
            [
                'name' => json_encode(['en' => 'Cape Verde', 'es' => 'Cabo Verde']),
                'iso2' => 'CV',
                'iso3' => 'CPV',
                'phone_code' => '+238'
            ],
            [
                'name' => json_encode(['en' => 'Central African Republic', 'es' => 'República Centroafricana']),
                'iso2' => 'CF',
                'iso3' => 'CAF',
                'phone_code' => '+236'
            ],
            [
                'name' => json_encode(['en' => 'Chad', 'es' => 'Chad']),
                'iso2' => 'TD',
                'iso3' => 'TCD',
                'phone_code' => '+235'
            ],
            [
                'name' => json_encode(['en' => 'Chile', 'es' => 'Chile']),
                'iso2' => 'CL',
                'iso3' => 'CHL',
                'phone_code' => '+56'
            ],
            [
                'name' => json_encode(['en' => 'China', 'es' => 'China']),
                'iso2' => 'CN',
                'iso3' => 'CHN',
                'phone_code' => '+86'
            ],
            [
                'name' => json_encode(['en' => 'Colombia', 'es' => 'Colombia']),
                'iso2' => 'CO',
                'iso3' => 'COL',
                'phone_code' => '+57'
            ],
            [
                'name' => json_encode(['en' => 'Comoros', 'es' => 'Comoras']),
                'iso2' => 'KM',
                'iso3' => 'COM',
                'phone_code' => '+269'
            ],
            [
                'name' => json_encode(['en' => 'Congo', 'es' => 'Congo']),
                'iso2' => 'CG',
                'iso3' => 'COG',
                'phone_code' => '+242'
            ],
            [
                'name' => json_encode(['en' => 'Costa Rica', 'es' => 'Costa Rica']),
                'iso2' => 'CR',
                'iso3' => 'CRI',
                'phone_code' => '+506'
            ],
            [
                'name' => json_encode(['en' => 'Croatia', 'es' => 'Croacia']),
                'iso2' => 'HR',
                'iso3' => 'HRV',
                'phone_code' => '+385'
            ],
            [
                'name' => json_encode(['en' => 'Cuba', 'es' => 'Cuba']),
                'iso2' => 'CU',
                'iso3' => 'CUB',
                'phone_code' => '+53'
            ],
            [
                'name' => json_encode(['en' => 'Cyprus', 'es' => 'Chipre']),
                'iso2' => 'CY',
                'iso3' => 'CYP',
                'phone_code' => '+357'
            ],
            [
                'name' => json_encode(['en' => 'Czech Republic', 'es' => 'República Checa']),
                'iso2' => 'CZ',
                'iso3' => 'CZE',
                'phone_code' => '+420'
            ],
            [
                'name' => json_encode(['en' => 'Denmark', 'es' => 'Dinamarca']),
                'iso2' => 'DK',
                'iso3' => 'DNK',
                'phone_code' => '+45'
            ],
            [
                'name' => json_encode(['en' => 'Djibouti', 'es' => 'Yibuti']),
                'iso2' => 'DJ',
                'iso3' => 'DJI',
                'phone_code' => '+253'
            ],
            [
                'name' => json_encode(['en' => 'Dominica', 'es' => 'Dominica']),
                'iso2' => 'DM',
                'iso3' => 'DMA',
                'phone_code' => '+1767'
            ],
            [
                'name' => json_encode(['en' => 'Dominican Republic', 'es' => 'República Dominicana']),
                'iso2' => 'DO',
                'iso3' => 'DOM',
                'phone_code' => '+1809'
            ],
            [
                'name' => json_encode(['en' => 'Ecuador', 'es' => 'Ecuador']),
                'iso2' => 'EC',
                'iso3' => 'ECU',
                'phone_code' => '+593'
            ],
            [
                'name' => json_encode(['en' => 'Egypt', 'es' => 'Egipto']),
                'iso2' => 'EG',
                'iso3' => 'EGY',
                'phone_code' => '+20'
            ],
            [
                'name' => json_encode(['en' => 'El Salvador', 'es' => 'El Salvador']),
                'iso2' => 'SV',
                'iso3' => 'SLV',
                'phone_code' => '+503'
            ],
            [
                'name' => json_encode(['en' => 'Equatorial Guinea', 'es' => 'Guinea Ecuatorial']),
                'iso2' => 'GQ',
                'iso3' => 'GNQ',
                'phone_code' => '+240'
            ],
            [
                'name' => json_encode(['en' => 'Eritrea', 'es' => 'Eritrea']),
                'iso2' => 'ER',
                'iso3' => 'ERI',
                'phone_code' => '+291'
            ],
            [
                'name' => json_encode(['en' => 'Estonia', 'es' => 'Estonia']),
                'iso2' => 'EE',
                'iso3' => 'EST',
                'phone_code' => '+372'
            ],
            [
                'name' => json_encode(['en' => 'Ethiopia', 'es' => 'Etiopía']),
                'iso2' => 'ET',
                'iso3' => 'ETH',
                'phone_code' => '+251'
            ],
            [
                'name' => json_encode(['en' => 'Fiji', 'es' => 'Fiyi']),
                'iso2' => 'FJ',
                'iso3' => 'FJI',
                'phone_code' => '+679'
            ],
            [
                'name' => json_encode(['en' => 'Finland', 'es' => 'Finlandia']),
                'iso2' => 'FI',
                'iso3' => 'FIN',
                'phone_code' => '+358'
            ],
            [
                'name' => json_encode(['en' => 'France', 'es' => 'Francia']),
                'iso2' => 'FR',
                'iso3' => 'FRA',
                'phone_code' => '+33'
            ],
            [
                'name' => json_encode(['en' => 'Gabon', 'es' => 'Gabón']),
                'iso2' => 'GA',
                'iso3' => 'GAB',
                'phone_code' => '+241'
            ],
            [
                'name' => json_encode(['en' => 'Gambia', 'es' => 'Gambia']),
                'iso2' => 'GM',
                'iso3' => 'GMB',
                'phone_code' => '+220'
            ],
            [
                'name' => json_encode(['en' => 'Georgia', 'es' => 'Georgia']),
                'iso2' => 'GE',
                'iso3' => 'GEO',
                'phone_code' => '+995'
            ],
            [
                'name' => json_encode(['en' => 'Germany', 'es' => 'Alemania']),
                'iso2' => 'DE',
                'iso3' => 'DEU',
                'phone_code' => '+49'
            ],
            [
                'name' => json_encode(['en' => 'Ghana', 'es' => 'Ghana']),
                'iso2' => 'GH',
                'iso3' => 'GHA',
                'phone_code' => '+233'
            ],
            [
                'name' => json_encode(['en' => 'Greece', 'es' => 'Grecia']),
                'iso2' => 'GR',
                'iso3' => 'GRC',
                'phone_code' => '+30'
            ],
            [
                'name' => json_encode(['en' => 'Grenada', 'es' => 'Granada']),
                'iso2' => 'GD',
                'iso3' => 'GRD',
                'phone_code' => '+1473'
            ],
            [
                'name' => json_encode(['en' => 'Guatemala', 'es' => 'Guatemala']),
                'iso2' => 'GT',
                'iso3' => 'GTM',
                'phone_code' => '+502'
            ],
            [
                'name' => json_encode(['en' => 'Guinea', 'es' => 'Guinea']),
                'iso2' => 'GN',
                'iso3' => 'GIN',
                'phone_code' => '+224'
            ],
            [
                'name' => json_encode(['en' => 'Guinea-Bissau', 'es' => 'Guinea-Bisáu']),
                'iso2' => 'GW',
                'iso3' => 'GNB',
                'phone_code' => '+245'
            ],
            [
                'name' => json_encode(['en' => 'Guyana', 'es' => 'Guyana']),
                'iso2' => 'GY',
                'iso3' => 'GUY',
                'phone_code' => '+592'
            ],
            [
                'name' => json_encode(['en' => 'Haiti', 'es' => 'Haití']),
                'iso2' => 'HT',
                'iso3' => 'HTI',
                'phone_code' => '+509'
            ],
            [
                'name' => json_encode(['en' => 'Honduras', 'es' => 'Honduras']),
                'iso2' => 'HN',
                'iso3' => 'HND',
                'phone_code' => '+504'
            ],
            [
                'name' => json_encode(['en' => 'Hungary', 'es' => 'Hungría']),
                'iso2' => 'HU',
                'iso3' => 'HUN',
                'phone_code' => '+36'
            ],
            [
                'name' => json_encode(['en' => 'Iceland', 'es' => 'Islandia']),
                'iso2' => 'IS',
                'iso3' => 'ISL',
                'phone_code' => '+354'
            ],
            [
                'name' => json_encode(['en' => 'India', 'es' => 'India']),
                'iso2' => 'IN',
                'iso3' => 'IND',
                'phone_code' => '+91'
            ],
            [
                'name' => json_encode(['en' => 'Indonesia', 'es' => 'Indonesia']),
                'iso2' => 'ID',
                'iso3' => 'IDN',
                'phone_code' => '+62'
            ],
            [
                'name' => json_encode(['en' => 'Iran', 'es' => 'Irán']),
                'iso2' => 'IR',
                'iso3' => 'IRN',
                'phone_code' => '+98'
            ],
            [
                'name' => json_encode(['en' => 'Iraq', 'es' => 'Irak']),
                'iso2' => 'IQ',
                'iso3' => 'IRQ',
                'phone_code' => '+964'
            ],
            [
                'name' => json_encode(['en' => 'Ireland', 'es' => 'Irlanda']),
                'iso2' => 'IE',
                'iso3' => 'IRL',
                'phone_code' => '+353'
            ],
            [
                'name' => json_encode(['en' => 'Israel', 'es' => 'Israel']),
                'iso2' => 'IL',
                'iso3' => 'ISR',
                'phone_code' => '+972'
            ],
            [
                'name' => json_encode(['en' => 'Italy', 'es' => 'Italia']),
                'iso2' => 'IT',
                'iso3' => 'ITA',
                'phone_code' => '+39'
            ],
            [
                'name' => json_encode(['en' => 'Jamaica', 'es' => 'Jamaica']),
                'iso2' => 'JM',
                'iso3' => 'JAM',
                'phone_code' => '+1876'
            ],
            [
                'name' => json_encode(['en' => 'Japan', 'es' => 'Japón']),
                'iso2' => 'JP',
                'iso3' => 'JPN',
                'phone_code' => '+81'
            ],
            [
                'name' => json_encode(['en' => 'Jordan', 'es' => 'Jordania']),
                'iso2' => 'JO',
                'iso3' => 'JOR',
                'phone_code' => '+962'
            ],
            [
                'name' => json_encode(['en' => 'Kazakhstan', 'es' => 'Kazajistán']),
                'iso2' => 'KZ',
                'iso3' => 'KAZ',
                'phone_code' => '+7'
            ],
            [
                'name' => json_encode(['en' => 'Kenya', 'es' => 'Kenia']),
                'iso2' => 'KE',
                'iso3' => 'KEN',
                'phone_code' => '+254'
            ],
            [
                'name' => json_encode(['en' => 'Kiribati', 'es' => 'Kiribati']),
                'iso2' => 'KI',
                'iso3' => 'KIR',
                'phone_code' => '+686'
            ],
            [
                'name' => json_encode(['en' => 'Kuwait', 'es' => 'Kuwait']),
                'iso2' => 'KW',
                'iso3' => 'KWT',
                'phone_code' => '+965'
            ],
            [
                'name' => json_encode(['en' => 'Kyrgyzstan', 'es' => 'Kirguistán']),
                'iso2' => 'KG',
                'iso3' => 'KGZ',
                'phone_code' => '+996'
            ],
            [
                'name' => json_encode(['en' => 'Laos', 'es' => 'Laos']),
                'iso2' => 'LA',
                'iso3' => 'LAO',
                'phone_code' => '+856'
            ],
            [
                'name' => json_encode(['en' => 'Latvia', 'es' => 'Letonia']),
                'iso2' => 'LV',
                'iso3' => 'LVA',
                'phone_code' => '+371'
            ],
            [
                'name' => json_encode(['en' => 'Lebanon', 'es' => 'Líbano']),
                'iso2' => 'LB',
                'iso3' => 'LBN',
                'phone_code' => '+961'
            ],
            [
                'name' => json_encode(['en' => 'Lesotho', 'es' => 'Lesoto']),
                'iso2' => 'LS',
                'iso3' => 'LSO',
                'phone_code' => '+266'
            ],
            [
                'name' => json_encode(['en' => 'Liberia', 'es' => 'Liberia']),
                'iso2' => 'LR',
                'iso3' => 'LBR',
                'phone_code' => '+231'
            ],
            [
                'name' => json_encode(['en' => 'Libya', 'es' => 'Libia']),
                'iso2' => 'LY',
                'iso3' => 'LBY',
                'phone_code' => '+218'
            ],
            [
                'name' => json_encode(['en' => 'Liechtenstein', 'es' => 'Liechtenstein']),
                'iso2' => 'LI',
                'iso3' => 'LIE',
                'phone_code' => '+423'
            ],
            [
                'name' => json_encode(['en' => 'Lithuania', 'es' => 'Lituania']),
                'iso2' => 'LT',
                'iso3' => 'LTU',
                'phone_code' => '+370'
            ],
            [
                'name' => json_encode(['en' => 'Luxembourg', 'es' => 'Luxemburgo']),
                'iso2' => 'LU',
                'iso3' => 'LUX',
                'phone_code' => '+352'
            ],
            [
                'name' => json_encode(['en' => 'Madagascar', 'es' => 'Madagascar']),
                'iso2' => 'MG',
                'iso3' => 'MDG',
                'phone_code' => '+261'
            ],
            [
                'name' => json_encode(['en' => 'Malawi', 'es' => 'Malaui']),
                'iso2' => 'MW',
                'iso3' => 'MWI',
                'phone_code' => '+265'
            ],
            [
                'name' => json_encode(['en' => 'Malaysia', 'es' => 'Malasia']),
                'iso2' => 'MY',
                'iso3' => 'MYS',
                'phone_code' => '+60'
            ],
            [
                'name' => json_encode(['en' => 'Maldives', 'es' => 'Maldivas']),
                'iso2' => 'MV',
                'iso3' => 'MDV',
                'phone_code' => '+960'
            ],
            [
                'name' => json_encode(['en' => 'Mali', 'es' => 'Malí']),
                'iso2' => 'ML',
                'iso3' => 'MLI',
                'phone_code' => '+223'
            ],
            [
                'name' => json_encode(['en' => 'Malta', 'es' => 'Malta']),
                'iso2' => 'MT',
                'iso3' => 'MLT',
                'phone_code' => '+356'
            ],
            [
                'name' => json_encode(['en' => 'Marshall Islands', 'es' => 'Islas Marshall']),
                'iso2' => 'MH',
                'iso3' => 'MHL',
                'phone_code' => '+692'
            ],
            [
                'name' => json_encode(['en' => 'Mauritania', 'es' => 'Mauritania']),
                'iso2' => 'MR',
                'iso3' => 'MRT',
                'phone_code' => '+222'
            ],
            [
                'name' => json_encode(['en' => 'Mauritius', 'es' => 'Mauricio']),
                'iso2' => 'MU',
                'iso3' => 'MUS',
                'phone_code' => '+230'
            ],
            [
                'name' => json_encode(['en' => 'Mexico', 'es' => 'México']),
                'iso2' => 'MX',
                'iso3' => 'MEX',
                'phone_code' => '+52'
            ],
            [
                'name' => json_encode(['en' => 'Micronesia', 'es' => 'Micronesia']),
                'iso2' => 'FM',
                'iso3' => 'FSM',
                'phone_code' => '+691'
            ],
            [
                'name' => json_encode(['en' => 'Moldova', 'es' => 'Moldavia']),
                'iso2' => 'MD',
                'iso3' => 'MDA',
                'phone_code' => '+373'
            ],
            [
                'name' => json_encode(['en' => 'Monaco', 'es' => 'Mónaco']),
                'iso2' => 'MC',
                'iso3' => 'MCO',
                'phone_code' => '+377'
            ],
            [
                'name' => json_encode(['en' => 'Mongolia', 'es' => 'Mongolia']),
                'iso2' => 'MN',
                'iso3' => 'MNG',
                'phone_code' => '+976'
            ],
            [
                'name' => json_encode(['en' => 'Montenegro', 'es' => 'Montenegro']),
                'iso2' => 'ME',
                'iso3' => 'MNE',
                'phone_code' => '+382'
            ],
            [
                'name' => json_encode(['en' => 'Morocco', 'es' => 'Marruecos']),
                'iso2' => 'MA',
                'iso3' => 'MAR',
                'phone_code' => '+212'
            ],
            [
                'name' => json_encode(['en' => 'Mozambique', 'es' => 'Mozambique']),
                'iso2' => 'MZ',
                'iso3' => 'MOZ',
                'phone_code' => '+258'
            ],
            [
                'name' => json_encode(['en' => 'Myanmar', 'es' => 'Myanmar']),
                'iso2' => 'MM',
                'iso3' => 'MMR',
                'phone_code' => '+95'
            ],
            [
                'name' => json_encode(['en' => 'Namibia', 'es' => 'Namibia']),
                'iso2' => 'NA',
                'iso3' => 'NAM',
                'phone_code' => '+264'
            ],
            [
                'name' => json_encode(['en' => 'Nauru', 'es' => 'Nauru']),
                'iso2' => 'NR',
                'iso3' => 'NRU',
                'phone_code' => '+674'
            ],
            [
                'name' => json_encode(['en' => 'Nepal', 'es' => 'Nepal']),
                'iso2' => 'NP',
                'iso3' => 'NPL',
                'phone_code' => '+977'
            ],
            [
                'name' => json_encode(['en' => 'Netherlands', 'es' => 'Países Bajos']),
                'iso2' => 'NL',
                'iso3' => 'NLD',
                'phone_code' => '+31'
            ],
            [
                'name' => json_encode(['en' => 'New Zealand', 'es' => 'Nueva Zelanda']),
                'iso2' => 'NZ',
                'iso3' => 'NZL',
                'phone_code' => '+64'
            ],
            [
                'name' => json_encode(['en' => 'Nicaragua', 'es' => 'Nicaragua']),
                'iso2' => 'NI',
                'iso3' => 'NIC',
                'phone_code' => '+505'
            ],
            [
                'name' => json_encode(['en' => 'Niger', 'es' => 'Níger']),
                'iso2' => 'NE',
                'iso3' => 'NER',
                'phone_code' => '+227'
            ],
            [
                'name' => json_encode(['en' => 'Nigeria', 'es' => 'Nigeria']),
                'iso2' => 'NG',
                'iso3' => 'NGA',
                'phone_code' => '+234'
            ],
            [
                'name' => json_encode(['en' => 'North Korea', 'es' => 'Corea del Norte']),
                'iso2' => 'KP',
                'iso3' => 'PRK',
                'phone_code' => '+850'
            ],
            [
                'name' => json_encode(['en' => 'North Macedonia', 'es' => 'Macedonia del Norte']),
                'iso2' => 'MK',
                'iso3' => 'MKD',
                'phone_code' => '+389'
            ],
            [
                'name' => json_encode(['en' => 'Norway', 'es' => 'Noruega']),
                'iso2' => 'NO',
                'iso3' => 'NOR',
                'phone_code' => '+47'
            ],
            [
                'name' => json_encode(['en' => 'Oman', 'es' => 'Omán']),
                'iso2' => 'OM',
                'iso3' => 'OMN',
                'phone_code' => '+968'
            ],
            [
                'name' => json_encode(['en' => 'Pakistan', 'es' => 'Pakistán']),
                'iso2' => 'PK',
                'iso3' => 'PAK',
                'phone_code' => '+92'
            ],
            [
                'name' => json_encode(['en' => 'Palau', 'es' => 'Palaos']),
                'iso2' => 'PW',
                'iso3' => 'PLW',
                'phone_code' => '+680'
            ],
            [
                'name' => json_encode(['en' => 'Panama', 'es' => 'Panamá']),
                'iso2' => 'PA',
                'iso3' => 'PAN',
                'phone_code' => '+507'
            ],
            [
                'name' => json_encode(['en' => 'Papua New Guinea', 'es' => 'Papúa Nueva Guinea']),
                'iso2' => 'PG',
                'iso3' => 'PNG',
                'phone_code' => '+675'
            ],
            [
                'name' => json_encode(['en' => 'Paraguay', 'es' => 'Paraguay']),
                'iso2' => 'PY',
                'iso3' => 'PRY',
                'phone_code' => '+595'
            ],
            [
                'name' => json_encode(['en' => 'Peru', 'es' => 'Perú']),
                'iso2' => 'PE',
                'iso3' => 'PER',
                'phone_code' => '+51'
            ],
            [
                'name' => json_encode(['en' => 'Philippines', 'es' => 'Filipinas']),
                'iso2' => 'PH',
                'iso3' => 'PHL',
                'phone_code' => '+63'
            ],
            [
                'name' => json_encode(['en' => 'Poland', 'es' => 'Polonia']),
                'iso2' => 'PL',
                'iso3' => 'POL',
                'phone_code' => '+48'
            ],
            [
                'name' => json_encode(['en' => 'Portugal', 'es' => 'Portugal']),
                'iso2' => 'PT',
                'iso3' => 'PRT',
                'phone_code' => '+351'
            ],
            [
                'name' => json_encode(['en' => 'Qatar', 'es' => 'Catar']),
                'iso2' => 'QA',
                'iso3' => 'QAT',
                'phone_code' => '+974'
            ],
            [
                'name' => json_encode(['en' => 'Romania', 'es' => 'Rumania']),
                'iso2' => 'RO',
                'iso3' => 'ROU',
                'phone_code' => '+40'
            ],
            [
                'name' => json_encode(['en' => 'Russia', 'es' => 'Rusia']),
                'iso2' => 'RU',
                'iso3' => 'RUS',
                'phone_code' => '+7'
            ],
            [
                'name' => json_encode(['en' => 'Rwanda', 'es' => 'Ruanda']),
                'iso2' => 'RW',
                'iso3' => 'RWA',
                'phone_code' => '+250'
            ],
            [
                'name' => json_encode(['en' => 'Saint Kitts and Nevis', 'es' => 'San Cristóbal y Nieves']),
                'iso2' => 'KN',
                'iso3' => 'KNA',
                'phone_code' => '+1869'
            ],
            [
                'name' => json_encode(['en' => 'Saint Lucia', 'es' => 'Santa Lucía']),
                'iso2' => 'LC',
                'iso3' => 'LCA',
                'phone_code' => '+1758'
            ],
            [
                'name' => json_encode(['en' => 'Saint Vincent and the Grenadines', 'es' => 'San Vicente y las Granadinas']),
                'iso2' => 'VC',
                'iso3' => 'VCT',
                'phone_code' => '+1784'
            ],
            [
                'name' => json_encode(['en' => 'Samoa', 'es' => 'Samoa']),
                'iso2' => 'WS',
                'iso3' => 'WSM',
                'phone_code' => '+685'
            ],
            [
                'name' => json_encode(['en' => 'San Marino', 'es' => 'San Marino']),
                'iso2' => 'SM',
                'iso3' => 'SMR',
                'phone_code' => '+378'
            ],
            [
                'name' => json_encode(['en' => 'Sao Tome and Principe', 'es' => 'Santo Tomé y Príncipe']),
                'iso2' => 'ST',
                'iso3' => 'STP',
                'phone_code' => '+239'
            ],
            [
                'name' => json_encode(['en' => 'Saudi Arabia', 'es' => 'Arabia Saudí']),
                'iso2' => 'SA',
                'iso3' => 'SAU',
                'phone_code' => '+966'
            ],
            [
                'name' => json_encode(['en' => 'Senegal', 'es' => 'Senegal']),
                'iso2' => 'SN',
                'iso3' => 'SEN',
                'phone_code' => '+221'
            ],
            [
                'name' => json_encode(['en' => 'Serbia', 'es' => 'Serbia']),
                'iso2' => 'RS',
                'iso3' => 'SRB',
                'phone_code' => '+381'
            ],
            [
                'name' => json_encode(['en' => 'Seychelles', 'es' => 'Seychelles']),
                'iso2' => 'SC',
                'iso3' => 'SYC',
                'phone_code' => '+248'
            ],
            [
                'name' => json_encode(['en' => 'Sierra Leone', 'es' => 'Sierra Leona']),
                'iso2' => 'SL',
                'iso3' => 'SLE',
                'phone_code' => '+232'
            ],
            [
                'name' => json_encode(['en' => 'Singapore', 'es' => 'Singapur']),
                'iso2' => 'SG',
                'iso3' => 'SGP',
                'phone_code' => '+65'
            ],
            [
                'name' => json_encode(['en' => 'Slovakia', 'es' => 'Eslovaquia']),
                'iso2' => 'SK',
                'iso3' => 'SVK',
                'phone_code' => '+421'
            ],
            [
                'name' => json_encode(['en' => 'Slovenia', 'es' => 'Eslovenia']),
                'iso2' => 'SI',
                'iso3' => 'SVN',
                'phone_code' => '+386'
            ],
            [
                'name' => json_encode(['en' => 'Solomon Islands', 'es' => 'Islas Salomón']),
                'iso2' => 'SB',
                'iso3' => 'SLB',
                'phone_code' => '+677'
            ],
            [
                'name' => json_encode(['en' => 'Somalia', 'es' => 'Somalia']),
                'iso2' => 'SO',
                'iso3' => 'SOM',
                'phone_code' => '+252'
            ],
            [
                'name' => json_encode(['en' => 'South Africa', 'es' => 'Sudáfrica']),
                'iso2' => 'ZA',
                'iso3' => 'ZAF',
                'phone_code' => '+27'
            ],
            [
                'name' => json_encode(['en' => 'South Korea', 'es' => 'Corea del Sur']),
                'iso2' => 'KR',
                'iso3' => 'KOR',
                'phone_code' => '+82'
            ],
            [
                'name' => json_encode(['en' => 'South Sudan', 'es' => 'Sudán del Sur']),
                'iso2' => 'SS',
                'iso3' => 'SSD',
                'phone_code' => '+211'
            ],
            [
                'name' => json_encode(['en' => 'Spain', 'es' => 'España']),
                'iso2' => 'ES',
                'iso3' => 'ESP',
                'phone_code' => '+34'
            ],
            [
                'name' => json_encode(['en' => 'Sri Lanka', 'es' => 'Sri Lanka']),
                'iso2' => 'LK',
                'iso3' => 'LKA',
                'phone_code' => '+94'
            ],
            [
                'name' => json_encode(['en' => 'Sudan', 'es' => 'Sudán']),
                'iso2' => 'SD',
                'iso3' => 'SDN',
                'phone_code' => '+249'
            ],
            [
                'name' => json_encode(['en' => 'Suriname', 'es' => 'Surinam']),
                'iso2' => 'SR',
                'iso3' => 'SUR',
                'phone_code' => '+597'
            ],
            [
                'name' => json_encode(['en' => 'Sweden', 'es' => 'Suecia']),
                'iso2' => 'SE',
                'iso3' => 'SWE',
                'phone_code' => '+46'
            ],
            [
                'name' => json_encode(['en' => 'Switzerland', 'es' => 'Suiza']),
                'iso2' => 'CH',
                'iso3' => 'CHE',
                'phone_code' => '+41'
            ],
            [
                'name' => json_encode(['en' => 'Syria', 'es' => 'Siria']),
                'iso2' => 'SY',
                'iso3' => 'SYR',
                'phone_code' => '+963'
            ],
            [
                'name' => json_encode(['en' => 'Taiwan', 'es' => 'Taiwán']),
                'iso2' => 'TW',
                'iso3' => 'TWN',
                'phone_code' => '+886'
            ],
            [
                'name' => json_encode(['en' => 'Tajikistan', 'es' => 'Tayikistán']),
                'iso2' => 'TJ',
                'iso3' => 'TJK',
                'phone_code' => '+992'
            ],
            [
                'name' => json_encode(['en' => 'Tanzania', 'es' => 'Tanzania']),
                'iso2' => 'TZ',
                'iso3' => 'TZA',
                'phone_code' => '+255'
            ],
            [
                'name' => json_encode(['en' => 'Thailand', 'es' => 'Tailandia']),
                'iso2' => 'TH',
                'iso3' => 'THA',
                'phone_code' => '+66'
            ],
            [
                'name' => json_encode(['en' => 'Timor-Leste', 'es' => 'Timor Oriental']),
                'iso2' => 'TL',
                'iso3' => 'TLS',
                'phone_code' => '+670'
            ],
            [
                'name' => json_encode(['en' => 'Togo', 'es' => 'Togo']),
                'iso2' => 'TG',
                'iso3' => 'TGO',
                'phone_code' => '+228'
            ],
            [
                'name' => json_encode(['en' => 'Tonga', 'es' => 'Tonga']),
                'iso2' => 'TO',
                'iso3' => 'TON',
                'phone_code' => '+676'
            ],
            [
                'name' => json_encode(['en' => 'Trinidad and Tobago', 'es' => 'Trinidad y Tobago']),
                'iso2' => 'TT',
                'iso3' => 'TTO',
                'phone_code' => '+1868'
            ],
            [
                'name' => json_encode(['en' => 'Tunisia', 'es' => 'Túnez']),
                'iso2' => 'TN',
                'iso3' => 'TUN',
                'phone_code' => '+216'
            ],
            [
                'name' => json_encode(['en' => 'Turkey', 'es' => 'Turquía']),
                'iso2' => 'TR',
                'iso3' => 'TUR',
                'phone_code' => '+90'
            ],
            [
                'name' => json_encode(['en' => 'Turkmenistan', 'es' => 'Turkmenistán']),
                'iso2' => 'TM',
                'iso3' => 'TKM',
                'phone_code' => '+993'
            ],
            [
                'name' => json_encode(['en' => 'Tuvalu', 'es' => 'Tuvalu']),
                'iso2' => 'TV',
                'iso3' => 'TUV',
                'phone_code' => '+688'
            ],
            [
                'name' => json_encode(['en' => 'Uganda', 'es' => 'Uganda']),
                'iso2' => 'UG',
                'iso3' => 'UGA',
                'phone_code' => '+256'
            ],
            [
                'name' => json_encode(['en' => 'Ukraine', 'es' => 'Ucrania']),
                'iso2' => 'UA',
                'iso3' => 'UKR',
                'phone_code' => '+380'
            ],
            [
                'name' => json_encode(['en' => 'United Arab Emirates', 'es' => 'Emiratos Árabes Unidos']),
                'iso2' => 'AE',
                'iso3' => 'ARE',
                'phone_code' => '+971'
            ],
            [
                'name' => json_encode(['en' => 'United Kingdom', 'es' => 'Reino Unido']),
                'iso2' => 'GB',
                'iso3' => 'GBR',
                'phone_code' => '+44'
            ],
            [
                'name' => json_encode(['en' => 'United States', 'es' => 'Estados Unidos']),
                'iso2' => 'US',
                'iso3' => 'USA',
                'phone_code' => '+1'
            ],
            [
                'name' => json_encode(['en' => 'Uruguay', 'es' => 'Uruguay']),
                'iso2' => 'UY',
                'iso3' => 'URY',
                'phone_code' => '+598'
            ],
            [
                'name' => json_encode(['en' => 'Uzbekistan', 'es' => 'Uzbekistán']),
                'iso2' => 'UZ',
                'iso3' => 'UZB',
                'phone_code' => '+998'
            ],
            [
                'name' => json_encode(['en' => 'Vanuatu', 'es' => 'Vanuatu']),
                'iso2' => 'VU',
                'iso3' => 'VUT',
                'phone_code' => '+678'
            ],
            [
                'name' => json_encode(['en' => 'Vatican City', 'es' => 'Ciudad del Vaticano']),
                'iso2' => 'VA',
                'iso3' => 'VAT',
                'phone_code' => '+39'
            ],
            [
                'name' => json_encode(['en' => 'Venezuela', 'es' => 'Venezuela']),
                'iso2' => 'VE',
                'iso3' => 'VEN',
                'phone_code' => '+58'
            ],
            [
                'name' => json_encode(['en' => 'Vietnam', 'es' => 'Vietnam']),
                'iso2' => 'VN',
                'iso3' => 'VNM',
                'phone_code' => '+84'
            ],
            [
                'name' => json_encode(['en' => 'Yemen', 'es' => 'Yemen']),
                'iso2' => 'YE',
                'iso3' => 'YEM',
                'phone_code' => '+967'
            ],
            [
                'name' => json_encode(['en' => 'Zambia', 'es' => 'Zambia']),
                'iso2' => 'ZM',
                'iso3' => 'ZMB',
                'phone_code' => '+260'
            ],
            [
                'name' => json_encode(['en' => 'Zimbabwe', 'es' => 'Zimbabue']),
                'iso2' => 'ZW',
                'iso3' => 'ZWE',
                'phone_code' => '+263'
            ],
        ];

        // Agregar IDs secuenciales a cada país si no los tienen
        foreach ($countries as $index => &$country) {
            if (!isset($country['id'])) {
                $country['id'] = $index + 1;
            }
        }

        // Usar upsert para evitar duplicados
        // Si existe un registro con el mismo iso2, se actualiza; si no, se inserta
        DB::table('countries')->upsert(
            $countries,
            ['iso2'], // Columna única para identificar duplicados
            ['name', 'iso3', 'phone_code'] // Columnas a actualizar si existe
        );
    }
}
