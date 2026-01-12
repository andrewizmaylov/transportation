# Grid vs Flexbox Examples

This document shows how to use both **Grid** and **Flexbox** layout options in your BDUI JSON files.

## How It Works

The renderer checks for layout properties in this priority:
1. If `grid` is specified → uses CSS Grid
2. If `flexbox` is specified → uses Flexbox
3. If neither is specified → no special layout (default block layout)

## Flexbox Example

Use Flexbox when you want items to flow naturally and wrap based on available space:

```json
{
  "columns": [
    {
      "components": [
        { "id": "field1", "type": "number" },
        { "id": "field2", "type": "number" },
        { "id": "field3", "type": "number" },
        { "id": "field4", "type": "number" }
      ],
      "flexbox": {
        "columnGap": "16px",
        "rowGap": "12px",
        "wrap": "wrap",
        "justify": "start"
      },
      "gridPosition": {
        "xs": 12
      }
    }
  ]
}
```

**Flexbox Properties:**
- `wrap: "wrap"` - Allows items to wrap to next line
- `columnGap: "16px"` - Space between items horizontally
- `rowGap: "12px"` - Space between rows when items wrap
- `justify: "start" | "end" | "center" | "between" | "around"` - Horizontal alignment

## Grid Example

Use Grid when you want precise control over column layout:

```json
{
  "columns": [
    {
      "components": [
        { "id": "field1", "type": "number" },
        { "id": "field2", "type": "number" },
        { "id": "field3", "type": "number" },
        { "id": "field4", "type": "number" }
      ],
      "grid": {
        "cols": 4,
        "columnGap": "16px",
        "rowGap": "12px"
      },
      "gridPosition": {
        "xs": 12
      }
    }
  ]
}
```

**Grid Properties:**
- `cols: 4` - Creates exactly 4 equal columns
- `cols: "auto-fit"` - Automatically fits columns based on available space
- `cols: "auto-fill"` - Fills space with as many columns as possible
- `columnGap: "16px"` - Space between columns
- `rowGap: "12px"` - Space between rows
- `gap: "16px"` - Shorthand for both columnGap and rowGap

## Real-World Examples

### Example 1: Dimensions Row (4 fields in one line)

**Using Flexbox:**
```json
{
  "columns": [
    {
      "components": [
        { "id": "lengthField", "type": "number" },
        { "id": "widthField", "type": "number" },
        { "id": "heightField", "type": "number" },
        { "id": "weightField", "type": "number" }
      ],
      "flexbox": {
        "columnGap": "16px",
        "wrap": "wrap"
      }
    }
  ]
}
```

**Using Grid (Recommended for fixed layout):**
```json
{
  "columns": [
    {
      "components": [
        { "id": "lengthField", "type": "number" },
        { "id": "widthField", "type": "number" },
        { "id": "heightField", "type": "number" },
        { "id": "weightField", "type": "number" }
      ],
      "grid": {
        "cols": 4,
        "columnGap": "16px"
      }
    }
  ]
}
```

### Example 2: Price and Currency (2 fields)

**Using Flexbox:**
```json
{
  "columns": [
    {
      "components": [
        { "id": "priceField", "type": "number" },
        { "id": "currencyField", "type": "select" }
      ],
      "flexbox": {
        "columnGap": "16px",
        "wrap": "wrap"
      }
    }
  ]
}
```

**Using Grid:**
```json
{
  "columns": [
    {
      "components": [
        { "id": "priceField", "type": "number" },
        { "id": "currencyField", "type": "select" }
      ],
      "grid": {
        "cols": 2,
        "columnGap": "16px"
      }
    }
  ]
}
```

### Example 3: Responsive Grid with Auto-fit

**Using Grid with auto-fit (responsive):**
```json
{
  "columns": [
    {
      "components": [
        { "id": "field1", "type": "input" },
        { "id": "field2", "type": "input" },
        { "id": "field3", "type": "input" }
      ],
      "grid": {
        "cols": "auto-fit",
        "columnGap": "16px",
        "rowGap": "12px"
      }
    }
  ]
}
```

This will automatically adjust the number of columns based on available space.

## When to Use Grid vs Flexbox

### Use **Grid** when:
- You need a fixed number of columns
- You want equal-width columns
- You need precise control over layout
- You want responsive columns that adapt (auto-fit/auto-fill)

### Use **Flexbox** when:
- You want items to wrap naturally
- You need flexible item sizing
- You want items to grow/shrink based on content
- You need alignment control (justify, align)

## Mixing Both in the Same Form

You can use different layout methods for different rows:

```json
{
  "rows": [
    {
      "columns": [
        {
          "components": [
            { "id": "nameField", "type": "input" }
          ]
        }
      ]
    },
    {
      "columns": [
        {
          "components": [
            { "id": "lengthField", "type": "number" },
            { "id": "widthField", "type": "number" },
            { "id": "heightField", "type": "number" }
          ],
          "grid": {
            "cols": 3,
            "columnGap": "16px"
          }
        }
      ]
    },
    {
      "columns": [
        {
          "components": [
            { "id": "priceField", "type": "number" },
            { "id": "currencyField", "type": "select" }
          ],
          "flexbox": {
            "columnGap": "16px",
            "wrap": "wrap"
          }
        }
      ]
    }
  ]
}
```

In this example:
- Row 1: No layout (single field, full width)
- Row 2: Uses **Grid** (3 equal columns)
- Row 3: Uses **Flexbox** (2 fields that can wrap)
