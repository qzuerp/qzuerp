import uuid
import json
import sys

def get_mac_address():
    try:
        mac = uuid.getnode()
        
        mac_hex = format(mac, '012x')
        
        mac_formatted = ':'.join(mac_hex[i:i+2] for i in range(0, 12, 2))
        
        return mac_formatted.upper()
        
    except Exception as e:
        return f"Hata: {str(e)}"

def main():
    try:
        mac_address = get_mac_address()
        
        result = {
            "success": True,
            "mac_address": mac_address,
            "message": "MAC adresi başarıyla alındı"
        }
        
        print(json.dumps(result, ensure_ascii=False))
        
    except Exception as e:
        error_result = {
            "success": False,
            "mac_address": None,
            "message": f"Hata oluştu: {str(e)}"
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)

if __name__ == "__main__":
    main()