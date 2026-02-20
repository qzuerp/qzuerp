<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap');

    .tablo-kapsayici {
        font-family: 'IBM Plex Sans', sans-serif;
        margin: 2rem;
        background: #0f1117;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 0 1px rgba(255,255,255,0.07), 0 24px 48px rgba(0,0,0,0.4);
    }

    .tablo-baslik {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #1a1d2e 0%, #161824 100%);
        border-bottom: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tablo-baslik span {
        width: 10px; height: 10px; border-radius: 50%;
    }
    .tablo-baslik span:nth-child(1) { background: #ff5f57; }
    .tablo-baslik span:nth-child(2) { background: #febc2e; }
    .tablo-baslik span:nth-child(3) { background: #28c840; }

    .tablo-sarici {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr {
        background: #1a1d2e;
    }

    thead th {
        padding: 0.85rem 1.25rem;
        text-align: left;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #6c7aad;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: background 0.15s ease;
    }

    tbody tr:last-child {
        border-bottom: none;
    }

    tbody tr:hover {
        background: rgba(108, 122, 173, 0.07);
    }

    tbody tr:nth-child(even) {
        background: rgba(255,255,255,0.015);
    }

    tbody tr:nth-child(even):hover {
        background: rgba(108, 122, 173, 0.07);
    }

    tbody td {
        padding: 0.8rem 1.25rem;
        font-size: 0.875rem;
        color: #c9cfe8;
        white-space: nowrap;
    }

    tbody td:first-child {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.8rem;
        color: #7c8cc8;
    }
</style>

<div class="tablo-kapsayici">
    <div class="tablo-baslik">
        <span></span><span></span><span></span>
    </div>
    <div class="tablo-sarici">
        <table>
            <thead>
                <tr>
                    @foreach($kolonlar as $k)
                        <th>{{ $k }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($sonuc as $row)
                    <tr>
                        @foreach($kolonlar as $k)
                            <td>{{ $row[$k] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>