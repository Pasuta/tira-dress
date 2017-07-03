
# Simple Diff for Python v 0.1
# (C) Paul Butler 2008 <http://www.paulbutler.org/>
# May be used and distributed under the zlib/libpng license
# <http://www.opensource.org/licenses/zlib-license.php>

def diff(old, new):
    """Find the differences between two lists. Returns a list of pairs, where the first value
    is in ['+','-','='] and represents an insertion, deletion, or no change for that list.
    The second value of the pair is the list of elements."""
    ohash = {}
    for i, val in enumerate(old): # Build a hash map with elements from old as keys, and
                                  # a list of indexes as values
        ohash.setdefault(val,[]).append(i)
    # Find the largest substring common to old and new
    lastRow = [0] * len(old)
    subStartOld = subStartNew = subLength = 0
    for j, val in enumerate(new):
        thisRow = [0] * len(old)
        for k in ohash.setdefault(val,[]):
            thisRow[k] = (k and lastRow[k - 1]) + 1
            if(thisRow[k] > subLength):
                subLength = thisRow[k]
                subStartOld = k - subLength + 1
                subStartNew = j - subLength + 1
        lastRow = thisRow
    if subLength == 0: # If no common substring is found, assume that an insert and 
                       # delete has taken place...
        return (old and [('-', old)] or []) + (new and [('+', new)] or [])
    else: # ...otherwise, the common substring is considered to have no change, and we recurse 
          # on the text before and after the substring
        return diff(old[:subStartOld], new[:subStartNew]) + \
               [('=', new[subStartNew:subStartNew + subLength])] + \
               diff(old[subStartOld + subLength:], new[subStartNew + subLength:])

# The below functions are intended for simple tests and experimentation; you will want to write more sophisticated wrapper functions for real use

def stringDiff(old, new):
    """Returns the difference between the old and new strings when split on whitespace. Considers punctuation a part of the word"""
    return diff(old.split(), new.split())

def htmlDiff(old, new):
    """Returns the difference between two strings (as in stringDiff) in HTML format."""
    con = {'=': (lambda x: x),
           '+': (lambda x: "<ins>" + x + "</ins>"),
           '-': (lambda x: "<del>" + x + "</del>")}
    return " ".join([(con[a])(" ".join(b)) for a, b in stringDiff(old, new)])

#Examples:
#print htmlDiff("The world is a tragedy to those who feel, but a comedy to those who think",
#  "Life is a tragedy for those who feel, and a comedy to those who think") # Horace Walpole

#print htmlDiff("I have often regretted my speech, never my silence",
#  "I have regretted my speech often, my silence never") # Xenocrates

