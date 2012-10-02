# Ubiquitous Language 
A language structured around the domain model and used by all team members to connect all the activities of the team with the software. (Eric Evans)

## Definitions
This is a list of words and definitions of the language.

### whitespace-safe
whitespace-safe as an property of an string means. Your unit test could compare the full string including its whitespace. *non* whitespace-safe means, that the unit test must not rely on the fact, that all whitespace is the same for equal data.
For example an HTML-String is considered as not whitespace-safe. Because these HTML-Snippets can be considered equal, allthough their whitespace is not:

```html
<li>
my string</li>
```
and
```html
<li>my string</li>
```
