"""
Wrapper script to fix Windows asyncio bug in Python 3.12
This script sets up the environment before importing problematic modules
"""

import sys
import os

# CRITICAL FIX for Windows Python 3.12 asyncio bug
# Set event loop policy BEFORE any asyncio import
if sys.platform == 'win32':
    # Prevent asyncio from loading
    import asyncio
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())

# Disable tqdm progress bars completely
os.environ['TQDM_DISABLE'] = '1'

# Now import the actual recommender
if __name__ == "__main__":
    # Import after fixes are applied
    from ai_recommender import main
    main()
