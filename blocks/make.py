import os
import json
import re

def generate_slug(title):
    """Converts a title to a slug (lowercase, hyphens instead of spaces, alphanumeric only)."""
    slug = re.sub(r'[^a-zA-Z0-9\s]', '', title)  # Remove special characters
    slug = slug.lower().replace(" ", "-")  # Replace spaces with hyphens
    return slug

def main():
    # Prompt user for input
    block_title = input("Enter block title: ")
    block_slug = generate_slug(block_title)  # Generate slug from title
    block_description = input("Enter block description: ")

    # Prompt for block icon with default value info
    block_icon = input("Enter block icon code (if left blank, it'll default to 'format-image'): ")
    if not block_icon.strip():
        block_icon = "format-image"

    # Default block category to "Complete Marketing"
    block_category = input("Enter block category slug (if left blank, it'll default to 'Complete Marketing'): ")
    if not block_category.strip():
        block_category = "Complete Marketing"

    block_keywords = input("Enter block keywords (comma separated): ").split(',')
    block_keywords = [keyword.strip() for keyword in block_keywords]

    # Prompt for ACF key
    acf_key = input("Enter the ACF key: ")

    # Create directory with block slug
    os.makedirs(block_slug, exist_ok=True)

    # Create file paths
    php_file_path = os.path.join(block_slug, f"{block_slug}.php")
    css_file_path = os.path.join(block_slug, f"{block_slug}.css")
    editor_css_file_path = os.path.join(block_slug, f"{block_slug}-editor.css")
    js_file_path = os.path.join(block_slug, f"{block_slug}.js")

    # Create empty files
    open(css_file_path, 'w').close()
    open(editor_css_file_path, 'w').close()
    open(js_file_path, 'w').close()

    # Create PHP boilerplate with container wrapper logic
    php_boilerplate = f"""<?php
$classes = '';
$id = '';
$acfKey = '{acf_key}';

if (!empty($block['className'])) {{
    $classes .= sprintf(' %s', $block['className']);
}}

if (!empty($block['anchor'])) {{
    $id = sprintf(' id=%s', $block['anchor']);
}}

?>
<section class=\"{block_slug}<?php echo esc_attr($classes); ?>\" <?php echo $id; ?> data-block-name=\"<?php echo $acfKey; ?>\">

</section>"""

    with open(php_file_path, 'w') as php_file:
        php_file.write(php_boilerplate)

    # Create block.JSON with the specified contents
    block_data = {
        "name": f"acf/cms-{block_slug}",
        "title": block_title,
        "description": block_description,
        "category": block_category,
        "editorStyle": [f"file:./{block_slug}-editor.css"],
        "script": f"file:./{block_slug}.js",
        "icon": block_icon,
        "keywords": block_keywords,
        "acf": {
            "mode": "edit",
            "renderTemplate": f"{block_slug}.php"
        },
        "supports": {
            "anchor": "true"
        },
        "align": "full"
    }

    with open(os.path.join(block_slug, "block.json"), 'w') as json_file:
        json.dump(block_data, json_file, indent=4)

    print(f"Directory and files for '{block_title}' (slug: {block_slug}) created successfully!")

if __name__ == "__main__":
    main()
