<?php
/**
 * Dızo Wear - Turkey Address API Helper
 * https://turkiyeapi.dev API entegrasyonu
 */

class TurkeyAddress {
    private const API_BASE = 'https://turkiyeapi.dev/api/v1';
    private const CACHE_DIR = __DIR__ . '/../../cache/address';
    private const CACHE_TTL = 86400 * 7; // 7 gün
    
    /**
     * Tüm illeri getir
     */
    public function getCities(): array {
        $cacheFile = self::CACHE_DIR . '/cities.json';
        
        // Cache kontrol
        if ($this->isCacheValid($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            return $data['data'] ?? [];
        }
        
        // API'den çek
        $response = $this->request('/provinces');
        
        if ($response && isset($response['data'])) {
            // Cache'e kaydet
            $this->saveCache($cacheFile, $response);
            return $response['data'];
        }
        
        return [];
    }
    
    /**
     * Belirli bir ilin ilçelerini getir
     */
    public function getDistricts(int $cityId): array {
        $cacheFile = self::CACHE_DIR . '/districts_' . $cityId . '.json';
        
        // Cache kontrol
        if ($this->isCacheValid($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            return $data['districts'] ?? [];
        }
        
        // API'den il bilgisini çek
        $response = $this->request('/provinces/' . $cityId);
        
        if ($response && isset($response['data']['districts'])) {
            $districts = $response['data']['districts'];
            
            // Cache'e kaydet
            $this->saveCache($cacheFile, ['districts' => $districts]);
            
            // Alfabetik sırala
            usort($districts, function($a, $b) {
                return strcoll($a['name'], $b['name']);
            });
            
            return $districts;
        }
        
        return [];
    }
    
    /**
     * Belirli bir ilçenin mahallelerini getir
     * Not: turkiyeapi.dev mahalle desteği sunmuyor, bu yüzden boş döner
     * İsterseniz başka bir API ile entegre edilebilir
     */
    public function getNeighborhoods(int $districtId): array {
        // turkiyeapi.dev mahalle bilgisi sunmuyor
        // Manuel mahalle girişi yapılabilir veya başka API kullanılabilir
        return [];
    }
    
    /**
     * İl adından il ID'si bul
     */
    public function getCityIdByName(string $cityName): ?int {
        $cities = $this->getCities();
        foreach ($cities as $city) {
            if (mb_strtolower($city['name']) === mb_strtolower($cityName)) {
                return $city['id'];
            }
        }
        return null;
    }
    
    /**
     * İl ve ilçe bilgilerini doğrula
     */
    public function validateAddress(string $cityName, string $districtName): bool {
        $cityId = $this->getCityIdByName($cityName);
        if (!$cityId) {
            return false;
        }
        
        $districts = $this->getDistricts($cityId);
        foreach ($districts as $district) {
            if (mb_strtolower($district['name']) === mb_strtolower($districtName)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * API isteği gönder
     */
    private function request(string $endpoint): ?array {
        $url = self::API_BASE . $endpoint;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: DizoWear/1.0'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('TurkeyAddress API Error: ' . $error);
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log('TurkeyAddress API HTTP Error: ' . $httpCode);
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Cache geçerli mi kontrol et
     */
    private function isCacheValid(string $file): bool {
        if (!file_exists($file)) {
            return false;
        }
        
        $mtime = filemtime($file);
        return (time() - $mtime) < self::CACHE_TTL;
    }
    
    /**
     * Cache'e kaydet
     */
    private function saveCache(string $file, array $data): void {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Cache'i temizle
     */
    public function clearCache(): void {
        $files = glob(self::CACHE_DIR . '/*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
