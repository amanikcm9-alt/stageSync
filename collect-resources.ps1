# collect-resources.ps1
# Run this from your project root (where the resources/ folder lives)

$resourcesPath = ".\resources"
$outputFile    = ".\output.txt"

# Clear (or create) output.txt
"" | Set-Content $outputFile

Get-ChildItem $resourcesPath -Recurse -File | ForEach-Object {

    # Build a clean relative path: resources\views\home.blade.php
    $relativePath = $_.FullName.Replace((Resolve-Path $resourcesPath).ToString(), "resources")

    # Write the filename header
    $header = "`n### FILE: $relativePath ###`n"
    Add-Content $outputFile -Value $header

    # Dump the file content
    Get-Content $_.FullName | Add-Content $outputFile

    # Add a blank line separator
    Add-Content $outputFile -Value "`n"
}

Write-Host "✅ Done! All resource files collected into: $outputFile"