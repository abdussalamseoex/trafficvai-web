import os
import glob

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # The current homepage mixes 'indigo' and 'orange'.
    # We want the primary theme to be 'blue', and accents 'brand' or 'orange'.
    # For exactly matching the vibe, let's swap 'indigo' to 'blue'
    content = content.replace('indigo-', 'blue-')
    
    # In hero.blade.php, the 'orange-600' should be 'blue-600' to match the image's blue CTA
    if 'hero.blade.php' in filepath:
        content = content.replace('bg-orange-600', 'bg-blue-600')
        content = content.replace('bg-orange-500', 'bg-blue-500')
        content = content.replace('shadow-orange-200', 'shadow-blue-200')
        content = content.replace('text-orange-600', 'text-blue-600')
        content = content.replace('text-gray-900 sm:text-6xl', 'text-blue-600 sm:text-6xl') # Making headline blue

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

# Apply to all home sections
for filepath in glob.glob('resources/views/home/sections/*.blade.php'):
    process_file(filepath)

# Let's fix tailwind.config.js to remove the indigo override so other things don't get messed up, 
# or change indigo to default blue just in case.
with open('tailwind.config.js', 'r', encoding='utf-8') as f:
    tw_content = f.read()

tw_content = tw_content.replace("indigo: colors.orange,", "// indigo: colors.orange,")

with open('tailwind.config.js', 'w', encoding='utf-8') as f:
    f.write(tw_content)

print("Colors updated successfully.")
