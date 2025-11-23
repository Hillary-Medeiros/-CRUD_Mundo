<?php
// IntegracaoApiEDITAR.php
//key do OpenWeather
define('OPENWEATHER_API_KEY', '2dc75fd4e648c85611812e39e7be3e52');

function makeApiRequest(string $url): ?array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        return null;
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    return $data;
}

/*função para poder escrever o nome do pais em português*/
function getCountryData(string $countryName): ?array
{
    // traduzindo pra ter o correspondente na api
    $allCountriesUrl = "https://restcountries.com/v3.1/all?fields=name,translations";
    $allData = makeApiRequest($allCountriesUrl);

    if (empty($allData)) {
        return null;
    }

    $englishName = null;

    foreach ($allData as $country) {
        if (isset($country['translations']['por']['common']) &&
            mb_strtolower($country['translations']['por']['common']) === mb_strtolower($countryName)) {
            $englishName = $country['name']['common'];
            break;
        }
    }

    // Se não encontrou tradução, tenta usar o nome recebido diretamente
    if (!$englishName) {
        $englishName = $countryName;
    }

    // Etapa 2: Buscar dados exatos do país em inglês
    $fields = 'capital,currencies,flags';
    $url = "https://restcountries.com/v3.1/name/" . urlencode($englishName) . "?fields=$fields&fullText=true";

    $data = makeApiRequest($url);

    if (empty($data) || !is_array($data)) {
        return null;
    }

    //tem que traduzir os campos que a api forneceu pra enviar pra tabela 
    $country = $data[0];
    $capital = $country['capital'][0] ?? 'N/A';
    $currencyCode = 'N/A';
    $currencyName = 'N/A';
    if (!empty($country['currencies'])) {
        $firstCurrencyKey = array_key_first($country['currencies']);
        if ($firstCurrencyKey) {
            $currencyCode = $firstCurrencyKey;
            $currencyName = $country['currencies'][$firstCurrencyKey]['name'] ?? 'N/A';
        }
    }
    $flagUrl = $country['flags']['svg'] ?? ($country['flags']['png'] ?? 'N/A');

    return [
        'capital' => $capital,
        'currency_code' => $currencyCode,
        'currency_name' => $currencyName,
        'flag_url' => $flagUrl,
    ];
}

/*Clima da api OpenWeather*/
function getWeatherByCity(string $cityName): ?array
{
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($cityName) . "&appid=" . OPENWEATHER_API_KEY . "&units=metric&lang=pt_br";
    $data = makeApiRequest($url);

    if (empty($data) || !is_array($data) || ($data['cod'] ?? 0) !== 200) {
        return null;
    }

    $weather = $data['weather'][0] ?? [];
    $main = $data['main'] ?? [];
    $wind = $data['wind'] ?? [];

    return [
        'description' => $weather['description'] ?? 'N/A',
        'icon' => $weather['icon'] ?? null,
        'temp' => $main['temp'] ?? 'N/A',
        'feels_like' => $main['feels_like'] ?? 'N/A',
        'temp_min' => $main['temp_min'] ?? 'N/A',
        'temp_max' => $main['temp_max'] ?? 'N/A',
        'pressure' => $main['pressure'] ?? 'N/A',
        'humidity' => $main['humidity'] ?? 'N/A',
        'wind_speed' => $wind['speed'] ?? 'N/A',
        'city_name' => $data['name'] ?? 'N/A',
        'country_code' => $data['sys']['country'] ?? 'N/A',
    ];
}

/**
 * Função para tratar requisições de API de edição (GET)
 * @param string $type 'cidade' ou 'pais'
 * @param string $name Nome da cidade ou país para busca
 */
function handleApiRequestForEdit(string $type, string $name): void
{
    header('Content-Type: application/json');

    if ($type === 'pais') {
        $data = getCountryData($name);
        if ($data) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'capital_Pais' => $data['capital'],
                    // Formatando a moeda para incluir nome e código
                    'moeda_Pais' => $data['currency_name'] . " (" . $data['currency_code'] . ")", 
                    'bandeira_Pais_url' => $data['flag_url']
                ]
            ]);
        } else {
            http_response_code(200); // OK para enviar a mensagem
            echo json_encode([
                'success' => false,
                'message' => 'País encontrado no banco, mas dados complementares da API RestCountries não encontrados ou erro na API.'
            ]);
        }
    } elseif ($type === 'cidade') {
        $data = getWeatherByCity($name);
        if ($data) {
            $clima_Formatado = "Clima: " . $data['description'] . 
                          " | Temp: " . round($data['temp']) . "°C" .
                          " | Sensação: " . round($data['feels_like']) . "°C";
            echo json_encode([
                'success' => true,
                'data' => [
                    'clima_Cidade' => $clima_Formatado,
                    'icone_Clima' => $data['icon']
                ]
            ]);
        } else {
            http_response_code(200); // OK para enviar a mensagem
            echo json_encode([
                'success' => false,
                'message' => 'Cidade encontrada no banco, mas dados de clima da API OpenWeather não encontrados ou erro na API.'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tipo de requisição de API inválido.']);
    }
}

?>