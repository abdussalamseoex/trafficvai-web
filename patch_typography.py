import re

with open('resources/views/client/dashboard.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('font-heading ', '')
text = text.replace('font-extrabold', 'font-bold')
text = text.replace('text-4xl font-bold text-gray-900', 'text-3xl font-bold text-gray-900')

with open('resources/views/client/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
