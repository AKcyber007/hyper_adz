import os
import re

# Files to update
TARGET_DIR = r"d:\UI design\resources\views\partner"
LAYOUT_FILE = r"d:\UI design\resources\views\layouts\partner.blade.php"

# Replacement map for dark to light mode
replacements = {
    # Layout specific
    'class="h-full bg-slate-950"': 'class="h-full bg-slate-50"',
    'body class="min-h-full flex bg-slate-950 text-slate-100': 'body class="min-h-full flex bg-slate-50 text-slate-800',
    'bg-slate-900/40 backdrop-blur-md sticky top-0': 'bg-white/80 backdrop-blur-md sticky top-0',
    'border-slate-850 bg-slate-900/40': 'border-slate-200 bg-white/80',
    'bg-slate-850 hover:bg-rose-950/40 text-xs font-bold text-slate-300 hover:text-rose-450 border-slate-750/80': 'bg-slate-100 hover:bg-rose-50 text-xs font-bold text-slate-600 hover:text-rose-600 border-slate-200',

    # Backgrounds
    'bg-slate-900/40': 'bg-white',
    'bg-slate-900/60': 'bg-slate-50',
    'bg-slate-950/50': 'bg-slate-50',
    'bg-slate-950/45': 'bg-slate-50',
    'bg-slate-950/40': 'bg-slate-100',
    'bg-slate-950': 'bg-white',
    'bg-slate-900': 'bg-white',
    'bg-slate-850': 'bg-slate-100',
    'bg-slate-800': 'bg-slate-100',
    
    # Borders
    'border-slate-850/60': 'border-slate-200',
    'border-slate-850': 'border-slate-200',
    'border-slate-800': 'border-slate-200',
    'border-slate-750': 'border-slate-200',
    'border-slate-700': 'border-slate-200',
    
    # Text colors
    'text-white': 'text-slate-900',
    'text-slate-100': 'text-slate-800',
    'text-slate-200': 'text-slate-700',
    'text-slate-300': 'text-slate-600',
    'text-slate-350': 'text-slate-600',
    'text-slate-400': 'text-slate-500',
    'text-slate-450': 'text-slate-500',
    'text-slate-655': 'text-slate-400',
}

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original = content
    for old, new in replacements.items():
        # Only replace exact class words or strings
        content = content.replace(old, new)
        
    if original != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {filepath}")

# Update layout
process_file(LAYOUT_FILE)

# Update all partner views
for root, dirs, files in os.walk(TARGET_DIR):
    for file in files:
        if file.endswith(".blade.php"):
            process_file(os.path.join(root, file))

print("Done")
