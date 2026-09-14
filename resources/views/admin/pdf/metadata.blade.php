<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $project['title'] }} Metadata</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2 {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

    <h2>{{ $project['title'] }} Metadata</h2>

    <h3>Project Info</h3>
    <table>
        <tr><th>Field</th><th>Value</th></tr>

        <tr><td>Title</td><td>{{ $project['title'] }}</td></tr>
        <tr><td>Type</td><td>{{ $project['type'] }}</td></tr>
        <tr><td>Genre</td><td>{{ $project['genre'] }}</td></tr>
        <tr><td>Subgenre</td><td>{{ $project['subgenre'] }}</td></tr>
        <tr><td>Language</td><td>{{ $project['language'] }}</td></tr>
        <tr><td>Explicit</td><td>{{ $project['explicit'] ? 'Yes' : 'No' }}</td></tr>
        <tr><td>Release Date</td><td>{{ $project['release_date'] }}</td></tr>
        <tr><td>Status</td><td>{{ $project['status'] }}</td></tr>
        <tr><td>Cover Path</td><td>{{ $project['cover_path'] }}</td></tr>
        <tr><td>Project Label</td><td>{{ $project['project_label'] ?? 'N/A' }}</td></tr>
        <tr><td>Project Songwriter</td><td>{{ $project['project_songwriter'] ?? 'N/A' }}</td></tr>
        <tr><td>Project UPC</td><td>{{ $project['project_upc'] ?? 'Pending' }}</td></tr>
        <tr><td>Created At</td><td>{{ $project['created_at'] }}</td></tr>
        <tr><td>Updated At</td><td>{{ $project['updated_at'] }}</td></tr>
    </table>

    <h3>Tracks</h3>
    <table>
        <tr>
            <th>Title</th>
            <th>Artist</th>
            <th>Featured Artists</th>
            <th>Producer</th>
            <th>ISRC</th>
            <th>Audio Path</th>
        </tr>
        @foreach($tracks as $track)
        <tr>
            <td>{{ $track['title'] }}</td>
            <td>{{ $track['artist'] }}</td>
            <td>{{ $track['featured_artists'] ?? 'N/A' }}</td>
            <td>{{ $track['producer'] ?? 'N/A' }}</td>
            <td>{{ $track['isrc'] ?? 'Pending' }}</td>
            <td>{{ $track['audio_path'] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </table>
    
     <h3>Profile Links</h3>
<table>
    <tr>
        <th>Platform</th>
        <th>Link</th>
    </tr>

    <tr>
        <td>Spotify</td>
        <td>{{ $artist['spotify'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Apple Music</td>
        <td>{{ $artist['apple_music'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Audiomack</td>
        <td>{{ $artist['audiomack'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Facebook</td>
        <td>{{ $artist['facebook'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Instagram</td>
        <td>{{ $artist['instagram'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>Twitter</td>
        <td>{{ $artist['twitter'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>TikTok</td>
        <td>{{ $artist['tiktok'] ?? 'N/A' }}</td>
    </tr>
</table>

</body>
</html>