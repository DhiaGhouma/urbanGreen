"""
Standalone wrapper to fix Windows Python 3.12 asyncio bug
This script MUST be called INSTEAD of ai_recommender.py directly
"""

import sys
import os

# CRITICAL: Set this BEFORE any import
if sys.platform == 'win32':
    # Force proactor event loop (alternative to selector)
    os.environ['PYTHONASYNCIODEBUG'] = '0'
    os.environ['PYTHONDONTWRITEBYTECODE'] = '1'

# Disable progress bars completely
os.environ['TQDM_DISABLE'] = '1'
os.environ['HF_HUB_DISABLE_PROGRESS_BARS'] = '1'
os.environ['TRANSFORMERS_NO_ADVISORY_WARNINGS'] = '1'

# Force UTF-8
os.environ['PYTHONIOENCODING'] = 'utf-8'
os.environ['PYTHONUTF8'] = '1'

# Monkey-patch asyncio BEFORE it's imported by any library
if sys.platform == 'win32':
    # Import asyncio and patch it immediately
    import asyncio
    import asyncio.windows_events
    
    # Store original
    _original_policy = asyncio.WindowsProactorEventLoopPolicy
    
    # Force WindowsSelectorEventLoopPolicy
    class FixedPolicy(asyncio.WindowsSelectorEventLoopPolicy):
        def __init__(self):
            super().__init__()
    
    # Override default
    asyncio.DefaultEventLoopPolicy = FixedPolicy
    asyncio.set_event_loop_policy(FixedPolicy())

# Now import the real script
if __name__ == "__main__":
    # Verify arguments
    if len(sys.argv) < 3:
        import json
        print(json.dumps({
            "error": "Usage: python wrapper.py <user_json> <greenspaces_json>"
        }), file=sys.stderr)
        sys.exit(1)
    
    # Import and run the main recommender
    # This import happens AFTER the asyncio fix
    try:
        from ai_recommender import main
        main()
    except Exception as e:
        import json
        print(json.dumps({
            "error": f"Wrapper error: {str(e)}"
        }), file=sys.stderr)
        sys.exit(1)
